import { Component, OnInit, inject } from '@angular/core';
import { WelcomeHeaderComponent } from "../shared/components/welcome-header/welcome-header.component";
import { ReactiveFormsModule, FormGroup, FormBuilder, Validators } from '@angular/forms';
import { SmallFooterComponent } from "../shared/components/small-footer/small-footer.component";
import { NgIconComponent, provideIcons } from "@ng-icons/core";
import { faEye, faEyeSlash } from "@ng-icons/font-awesome/regular";

@Component({
    selector: 'app-login',
    imports: [
        WelcomeHeaderComponent,
        ReactiveFormsModule,
        SmallFooterComponent,
        NgIconComponent,
    ],
    providers: [
        provideIcons({ faEye, faEyeSlash }),
    ],
    template: `
        <div class="row vh-100 d-flex justify-content-center align-items-center">
            <div class="col-3">
                <app-welcome-header/>

                <div class="login-card shadow-sm p-4 mt-3 bg-white">
                    <div class="login-card-header">
                        <h5>Login</h5>
                        <p class="text-muted small mt-1">Enter your credentials to access your account</p>
                    </div>
                    <div class="login-card-body">
                        <form [formGroup]="form">
                            <div class="form-group mt-4">
                                <label for="email">Email</label>
                                <input
                                    id="email"
                                    type="text"
                                    class="form-control mt-2"
                                    formControlName="email"
                                    placeholder="user@example.com"
                                />
                            </div>

                            <div class="form-group mt-4">
                                <div class="d-flex justify-content-between">
                                    <label for="password">Password</label>
                                    <a class="small text-decoration-none">Forgot password?</a>
                                </div>
                                <div class="position-relative">
                                    <input
                                        id="password"
                                        type="{{ showPassword ? 'text' : 'password' }}"
                                        class="form-control mt-2"
                                        formControlName="password"
                                        placeholder="&bull;&bull;&bull;&bull;"
                                    >
                                    <ng-icon 
                                        class="password-toggle-icon" 
                                        name="{{ showPassword ? 'faEyeSlash' : 'faEye' }}"
                                        size="20px"
                                        (click)="togglePassword()"
                                    ></ng-icon>
                                </div>
                            </div>

                            <div>
                                <button class="btn btn-primary w-100 mt-4">
                                    Sign in
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="login-card-footer text-center mt-4">
                        <small>Don't have an account? <a routerLink="/signup" class="text-decoration-none">Sign up</a></small>
                    </div>
                </div>

                <app-small-footer/>
            </div>
        </div>
    `,
    styles: [`
        .login-card {
            border: 1px solid #cce5ff;
            border-radius: 10px;
        }

        .password-toggle-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 15px;
            cursor: pointer;
        }
    `]
})
export class LoginComponent implements OnInit {
    protected formBuilder: FormBuilder = inject(FormBuilder);

    protected form!: FormGroup;
    protected showPassword: boolean = false;

    ngOnInit(): void {
        this.initForm();
    }

    protected togglePassword(): void {
        this.showPassword = !this.showPassword;
    }

    protected initForm(): void {
        this.form = this.formBuilder.group({
            email: ['', Validators.required],
            password: ['', Validators.required],
        });
    }
}
