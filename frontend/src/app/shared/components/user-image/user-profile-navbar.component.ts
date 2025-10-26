import { Component, inject, Signal } from '@angular/core';
import { NgIcon, NgIconComponent, provideIcons } from '@ng-icons/core';
import { faUser } from '@ng-icons/font-awesome/regular';
import { faSolidChevronDown, faSolidGear, faSolidRightFromBracket } from '@ng-icons/font-awesome/solid';
import { Store } from '@ngxs/store';
import { UserState } from '../../store/user/user.state';
import { User } from '../../types/user.types';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { AuthService } from '../../services/auth/auth.service';
import { ToastService } from '../../services/toast/toast.service';
import { ToastType } from '../../enums/enums';
import { LogoutUser } from '../../store/user/user.actions';
import { Router } from '@angular/router';

@Component({
    selector: 'app-user-profile-navbar',
    imports: [NgIcon, TranslatePipe, NgIconComponent],
    providers: [provideIcons({faUser, faSolidChevronDown, faSolidGear, faSolidRightFromBracket})],
    template: `
        <div class="dropdown">
                <button class="btn bg-transparent border-0 p-0 ms-2 dropdown-toggle text-primary" data-bs-toggle="dropdown">{{user()?.username}}</button>
                <ul class="dropdown-menu mt-1">
                    <li>
                        <button class="dropdown-item" type="button">
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <span>{{"navbar.settings" | translate}}</span>
                            </div>
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item navbar-logout-item" type="button" (click)="logout()">
                            <div class="d-flex align-items-center justify-content-start gap-2">
                                <span>{{"navbar.logout" | translate}}</span>
                            </div>
                        </button>
                    </li>
                </ul>
            </div>
    `,
    styles: [`
        .navbar-logout-item {
            color: var(--text-danger-color);

            &:active {
                color: var(--text-secondary-color);
                background-color: var(--text-danger-color);
            }
        }
    `]
})
export class UserProfileNavbarComponent {
    private readonly authService = inject(AuthService);
    private readonly store = inject(Store);
    private readonly toast = inject(ToastService);
    private readonly router = inject(Router);
    private readonly translate = inject(TranslateService);

    protected user: Signal<User | null> = this.store.selectSignal(UserState.userData);

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
