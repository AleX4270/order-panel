import { WelcomeHeaderComponent } from "../shared/components/welcome-header/welcome-header.component";
import { Component } from '@angular/core';
import { ReactiveFormsModule, FormGroup } from '@angular/forms';
import { SmallFooterComponent } from "../shared/components/small-footer/small-footer.component";

@Component({
    selector: 'app-login',
    imports: [
    WelcomeHeaderComponent,
    ReactiveFormsModule,
    SmallFooterComponent
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
                                <input
                                    id="password"
                                    type="password"
                                    class="form-control mt-2"
                                    formControlName="password"
                                    placeholder="&bull;&bull;&bull;&bull;"
                                />
                                <!-- TODO: Password toggle button -->
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
    `]
})
export class LoginComponent {
    protected form!: FormGroup;
}
