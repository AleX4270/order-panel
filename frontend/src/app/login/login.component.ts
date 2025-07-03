import { Component, OnInit, inject } from '@angular/core';
import { WelcomeHeaderComponent } from "../shared/components/welcome-header/welcome-header.component";
import { ReactiveFormsModule, FormGroup, FormBuilder, Validators } from '@angular/forms';
import { SmallFooterComponent } from "../shared/components/small-footer/small-footer.component";
import { NgIconComponent, provideIcons } from "@ng-icons/core";
import { faEye, faEyeSlash } from "@ng-icons/font-awesome/regular";
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-login',
    imports: [
        WelcomeHeaderComponent,
        ReactiveFormsModule,
        SmallFooterComponent,
        NgIconComponent,
        CommonModule,
        RouterModule,
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
                                </div>
                                <div class="position-relative">
                                    <input
                                        id="password"
                                        type="{{ showPassword ? 'text' : 'password' }}"
                                        class="form-control mt-2"
                                        formControlName="password"
                                        placeholder="&bull;&bull;&bull;&bull;"
                                    >
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
                                <button class="btn btn-primary w-100 mt-4">
                                    Sign in
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="login-card-footer text-center mt-4 d-flex justify-content-center align-items-center">
                        <small>Forgot your password?</small> <button routerLink="/password-recovery" class="btn btn-sm btn-link">Recover it!</button>
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
            background-color: white;
            padding-left: 6px;
            position: absolute;
            top: 59%;
            transform: translateY(-50%);
            right: 10px;
            cursor: pointer;
        }
    `]
})
export class LoginComponent implements OnInit {
    private readonly translate: TranslateService = inject(TranslateService);
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
