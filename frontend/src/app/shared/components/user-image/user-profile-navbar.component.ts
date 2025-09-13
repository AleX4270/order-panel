import { Component, inject, Signal } from '@angular/core';
import { NgIcon, provideIcons } from '@ng-icons/core';
import { faUser } from '@ng-icons/font-awesome/regular';
import { faSolidChevronDown } from '@ng-icons/font-awesome/solid';
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
    imports: [NgIcon, TranslatePipe],
    providers: [provideIcons({faUser, faSolidChevronDown})],
    template: `
        <!-- TODO: Implement a real user image loading mechanism -->
        <!-- TODO: To musi być zbudowane inaczej, nie da się tak ustawić calej szerokosci dropdowna i ten placeholder zdjecia przeszkadza -->
        <div class="user-data-container d-flex justify-content-start align-items-center">
            <div class="user-image-container d-flex justify-content-center align-items-center">
                <ng-icon
                    name="fa-user"
                    size="21px"
                ></ng-icon>
            </div>

            <div class="dropdown">
                <button class="btn bg-transparent border-0 p-0 ms-2 dropdown-toggle" data-bs-toggle="dropdown">{{user()?.username}}</button>
                <ul class="dropdown-menu mt-1">
                    <li><button class="dropdown-item" type="button">{{"navbar.settings" | translate}}</button></li>
                    <li><button class="dropdown-item navbar-logout-item" type="button" (click)="logout()">{{"navbar.logout" | translate}}</button></li>
                </ul>
            </div>
        </div>
    `,
    styles: [`
        .user-data-container {
            cursor: pointer;
        }

        .user-image-container {
            border: 2px solid black;
            width: 39px;
            height: 39px;
            border-radius: 100%;
        }

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
                    this.toast.show(this.translate.instant('logout.success'), ToastType.success);
                });
            },
            error: () => {
                this.toast.show(this.translate.instant('logout.error'), ToastType.danger);
            },
        });
    }
}
