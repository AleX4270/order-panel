import { Component, inject, OnInit, Signal } from '@angular/core';
import { SmallFooterComponent } from "../small-footer/small-footer.component";
import { Store } from '@ngxs/store';
import { UserState } from '../../store/user/user.state';
import { LocalDataService } from '../../services/local-data/local-data.service';
import { NavbarElement } from '../../types/navbar.types';
import { DropdownComponent } from "../dropdown/dropdown.component";
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { AuthService } from '../../services/api/auth/auth.service';
import { ToastService } from '../../services/toast/toast.service';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { LogoutUser } from '../../store/user/user.actions';
import { ToastType } from '../../enums/enums';
import { User } from '../../types/user.types';
import { NgTemplateOutlet } from '@angular/common';

@Component({
    selector: 'app-navbar',
    imports: [SmallFooterComponent, DropdownComponent, TranslatePipe, RouterLink, RouterLinkActive, NgTemplateOutlet],
    template: `
        <ng-template #navLinks>
            @for(navElement of navbarElementList; track navElement) {
                <li>
                    <a [routerLink]="navElement.url" routerLinkActive="text-primary" class="hover:text-primary">
                        {{"navbar." + navElement.label | translate}}
                    </a>
                </li>
            }
        </ng-template>

        <nav class="navbar bg-base-100 shadow-sm">
            <div class="navbar-start">
                <div class="hidden lg:flex">
                    <app-dropdown>
                        <ng-template #label><span class="text-primary">{{user()?.username}}</span></ng-template>
                        <ng-template #options>
                            <li>
                                <button class="btn btn-ghost" type="button" (click)="logout()">
                                    <span class="text-error">{{"navbar.logout" | translate}}</span>
                                </button>
                            </li>
                        </ng-template>
                    </app-dropdown>
                </div>
                <app-dropdown class="lg:hidden">
                    <ng-template #label>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" /> </svg>
                    </ng-template>
                    <ng-template #options>
                        @if(isUserAuthenticated()) {
                            <li><span class="text-primary font-semibold pointer-events-none">{{user()?.username}}</span></li>

                            <ng-container *ngTemplateOutlet="navLinks"></ng-container>

                            <li>
                                <button class="btn btn-ghost" type="button" (click)="logout()">
                                    <span class="text-error">{{"navbar.logout" | translate}}</span>
                                </button>
                            </li>
                        }
                    </ng-template>
                </app-dropdown>
            </div>
            <div class="navbar-center hidden lg:flex">
                <ul class="menu menu-horizontal">
                    @if(isUserAuthenticated()) {
                        <ng-container *ngTemplateOutlet="navLinks"></ng-container>
                    }
                </ul>
            </div>
            <div class="navbar-end">
                <app-small-footer/>
            </div>
        </nav>
    `,
    styles: [``],
})
export class NavbarComponent implements OnInit {
    private readonly localDataService = inject(LocalDataService);
    private readonly authService = inject(AuthService);
    private readonly store = inject(Store);
    private readonly toast = inject(ToastService);
    private readonly router = inject(Router);
    private readonly translate = inject(TranslateService);

    protected user: Signal<User | null> = this.store.selectSignal(UserState.userData);
    protected isUserAuthenticated = this.store.selectSignal(UserState.isAuthenticated);
    protected navbarElementList: NavbarElement[] = [];

    ngOnInit(): void {
        this.localDataService.getNavbarData().subscribe({
            next: (res) => {
                this.navbarElementList = res;
            },
            error: (err) => {
                console.error(err);
            }
        });
    }

    protected logout(): void {
        this.authService.logout().subscribe({
            next: () => {
                this.store.dispatch(new LogoutUser);
                this.router.navigate(['/']).then(() => {
                    this.toast.show(this.translate.instant('logout.success'), ToastType.warning);
                });
            },
            error: () => {
                this.toast.show(this.translate.instant('logout.error'), ToastType.danger);
            },
        });
    }
}
