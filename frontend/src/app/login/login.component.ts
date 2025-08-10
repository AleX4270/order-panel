import { Component, OnInit, inject } from "@angular/core";
import { WelcomeHeaderComponent } from "../shared/components/welcome-header/welcome-header.component";
import {
    ReactiveFormsModule,
    FormGroup,
    FormBuilder,
    Validators,
} from "@angular/forms";
import { SmallFooterComponent } from "../shared/components/small-footer/small-footer.component";
import { NgIconComponent, provideIcons } from "@ng-icons/core";
import { faEye, faEyeSlash } from "@ng-icons/font-awesome/regular";
import { CommonModule } from "@angular/common";
import { Router, RouterModule } from "@angular/router";
import { TranslatePipe, TranslateService } from "@ngx-translate/core";
import { AuthService } from "../shared/services/auth/auth.service";
import { UserLoginCredentials } from "../shared/types/auth.types";
import { ToastService } from "../shared/services/toast/toast.service";
import { LanguageType, ToastType } from "../shared/enums/enums";
import { Store } from "@ngxs/store";
import { User } from "../shared/types/user.types";
import { LoginUser } from "../shared/store/user/user.actions";

@Component({
    selector: "app-login",
    imports: [
        WelcomeHeaderComponent,
        ReactiveFormsModule,
        SmallFooterComponent,
        NgIconComponent,
        CommonModule,
        RouterModule,
        TranslatePipe,
    ],
    providers: [provideIcons({ faEye, faEyeSlash })],
    template: `
        <div class="row vh-100 d-flex justify-content-center align-items-center">
            <div class="col-3">
                <app-welcome-header />

                <div class="login-card shadow-sm p-4 mt-3 bg-white">
                    <div class="login-card-header">
                        <h5>{{ "login.title" | translate }}</h5>
                        <p class="text-muted small mt-1">
                            {{ "login.description" | translate }}
                        </p>
                    </div>
                    <div class="login-card-body">
                        <form [formGroup]="form">
                            <div class="form-group mt-4">
                                <label for="email">{{ "login.email" | translate }}</label>
                                <input
                                    id="email"
                                    type="text"
                                    class="form-control mt-2"
                                    formControlName="email"
                                    placeholder="{{ 'login.emailPlaceholder' | translate }}"
                                />
                            </div>

                            <div class="form-group mt-4">
                                <div class="d-flex justify-content-between">
                                    <label for="password">{{
                                        "login.password" | translate
                                    }}</label>
                                </div>
                                <div class="position-relative">
                                    <input
                                        id="password"
                                        type="{{ showPassword ? 'text' : 'password' }}"
                                        class="form-control mt-2"
                                        formControlName="password"
                                        placeholder="&bull;&bull;&bull;&bull;"
                                    />
                                    <div class="password-toggle-icon">
                                        <ng-icon
                                            name="{{ showPassword ? 'faEyeSlash' : 'faEye' }}"
                                            size="20px"
                                            (click)="togglePassword()"
                                        ></ng-icon>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <button class="btn btn-primary w-100 mt-4" (click)="onSubmit()">
                                    {{ "login.submit" | translate }}
                                </button>
                            </div>
                        </form>
                    </div>
                    <div
                        class="login-card-footer text-center mt-4 d-flex justify-content-center align-items-center"
                    >
                        <small>{{ "login.forgotPassword" | translate }}</small>
                        <button routerLink="/password-recovery" class="btn btn-sm btn-link">
                            {{ "login.passwordRecovery" | translate }}
                        </button>
                    </div>
                </div>

                <div class="mt-3">
                    <app-small-footer />
                </div>
            </div>
        </div>
    `,
    styles: [
        `
            .login-card {
                border: 1px solid #cce5ff;
                border-radius: 10px;
            }

            .password-toggle-icon {
                background-color: white;
                padding-left: 6px;
                position: absolute;
                top: 59%;
                transform: translateY(-50%);
                right: 10px;
                cursor: pointer;
            }
        `,
    ],
})
export class LoginComponent implements OnInit {
    private readonly translate: TranslateService = inject(TranslateService);
    private readonly authService: AuthService = inject(AuthService);
    private readonly toast: ToastService = inject(ToastService);
    private readonly router: Router = inject(Router);
    private readonly store: Store = inject(Store);

    protected formBuilder: FormBuilder = inject(FormBuilder);
    protected form!: FormGroup;
    protected showPassword: boolean = false;

    private isFormSubmitted: boolean = false;

    ngOnInit(): void {
        this.initForm();
    }

    protected togglePassword(): void {
        this.showPassword = !this.showPassword;
    }

    protected initForm(): void {
        this.form = this.formBuilder.group({
            email: ["", Validators.required],
            password: ["", Validators.required],
        });
    }

    protected onSubmit(): void {
        if (this.isFormSubmitted) {
            return;
        }

        if (!this.form.valid) {
            this.toast.show(this.translate.instant("form.error"), ToastType.danger);
            return;
        }

        const userCredentials: UserLoginCredentials = {
            email: this.form.get("email")?.value,
            password: this.form.get("password")?.value,
        };

        this.authService.login(userCredentials).subscribe({
            next: (res) => {
                if(res.data) {
                    const userData: User = {
                        username: res.data.name,
                        isAuthenticated: true,
                    };

                    this.store.dispatch(new LoginUser(userData));
                    this.router.navigate(['/dashboard']);
                }
            },
            error: (err) => {
                console.error(err);
                this.toast.show(
                    this.translate.instant("login.error"),
                    ToastType.danger,
                );
            },
        });
    }
}
