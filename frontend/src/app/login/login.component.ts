import { Component, OnInit, WritableSignal, inject, signal } from "@angular/core";
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
import { AuthService } from "../shared/services/api/auth/auth.service";
import { UserLoginCredentials } from "../shared/types/auth.types";
import { ToastService } from "../shared/services/toast/toast.service";
import { ToastType } from "../shared/enums/enums";
import { Store } from "@ngxs/store";
import { User } from "../shared/types/user.types";
import { LoginUser } from "../shared/store/user/user.actions";
import { finalize } from "rxjs";
import { ButtonComponent } from "../shared/components/button/button.component";
import { InputErrorLabelComponent } from "../shared/components/input-error-label/input-error-label.component";

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
    ButtonComponent,
    InputErrorLabelComponent
],
    providers: [provideIcons({ faEye, faEyeSlash })],
    template: `
        <div class="min-h-screen flex items-center justify-center px-2 sm:px-4">
            <div class="w-full max-w-md">
                <div class="w-full flex justify-center pb-5">
                    <app-welcome-header />
                </div>

                <div class="card sm:card-border sm:p-1 sm:shadow-sm sm:shadow-primary/10">
                    <div class="card-body">
                        <h5 class="card-title text-xl">{{ "login.title" | translate }}</h5>
                        <p class="text-base-content/60 mt-1">
                            {{ "login.description" | translate }}
                        </p>

                        <form [formGroup]="form" (ngSubmit)="onSubmit()">
                            <div class="mt-5 flex flex-col">
                                <label class="label" for="email">{{ "login.email" | translate }}</label>
                                <input
                                    id="email"
                                    type="email"
                                    class="input w-full"
                                    formControlName="email"
                                    [placeholder]="'login.emailPlaceholder' | translate"
                                />
                                <app-input-error-label [control]="form.get('email')" />
                            </div>

                            <div class="mt-7 flex flex-col">
                                <div class="flex justify-between">
                                    <label class="label" for="password">{{
                                        "login.password" | translate
                                    }}</label>
                                </div>
                                <div class="relative">
                                    <input
                                        id="password"
                                        [type]="showPassword ? 'text' : 'password'"
                                        class="input w-full"
                                        formControlName="password"
                                        placeholder="&bull;&bull;&bull;&bull;"
                                    />
                                    <app-input-error-label [control]="form.get('password')" />
                                    <div class="absolute right-3 -translate-y-1/2 top-5 cursor-pointer">
                                        <ng-icon
                                            class="item-pressable"
                                            [name]="showPassword ? 'faEyeSlash' : 'faEye'"
                                            size="20px"
                                            (click)="togglePassword()"
                                        ></ng-icon>
                                    </div>
                                </div>
                            </div>

                            <div class="card-actions w-full">
                                <app-button
                                    class="w-full"
                                    classList="w-full btn btn-primary mt-10"
                                    type="submit"
                                    [isDisabled]="isSubmitted()"
                                    [isLoading]="isSubmitted()"
                                >{{'login.submit' | translate}}</app-button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="text-center flex justify-center items-center">
                        <!-- <small>{{ "login.forgotPassword" | translate }}</small>
                        <button routerLink="/password-recovery" class="btn btn-sm btn-link">
                            {{ "login.passwordRecovery" | translate }}
                        </button> -->
                    </div>
                </div>

                <div class="mt-3">
                    <app-small-footer />
                </div>
            </div>
        </div>
    `,
    styles: [],
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

    protected isSubmitted: WritableSignal<boolean> = signal<boolean>(false);

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
        if (this.isSubmitted()) {
            return;
        }

        if (!this.form.valid) {
            this.toast.show(this.translate.instant("form.error"), ToastType.danger);
            return;
        }

        this.isSubmitted.set(true);

        const userCredentials: UserLoginCredentials = {
            email: this.form.get("email")?.value,
            password: this.form.get("password")?.value,
        };

        this.authService.login(userCredentials)
        .pipe(
            finalize(() => this.isSubmitted.set(false)),
        )
        .subscribe({
            next: (res) => {
                if(res.data) {
                    const userData: User = {
                        username: res.data.name,
                        isAuthenticated: true,
                    };

                    this.store.dispatch(new LoginUser(userData));
                    this.router.navigate(['/orders']);
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
