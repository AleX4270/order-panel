import { Component, OnInit, inject } from '@angular/core';
import { WelcomeHeaderComponent } from "../shared/components/welcome-header/welcome-header.component";
import { ReactiveFormsModule, FormGroup, FormBuilder, Validators } from '@angular/forms';
import { SmallFooterComponent } from "../shared/components/small-footer/small-footer.component";
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-login',
    imports: [
        WelcomeHeaderComponent,
        ReactiveFormsModule,
        SmallFooterComponent,
        TranslatePipe
    ],
    template: `
        <div class="row vh-100 d-flex justify-content-center align-items-center">
            <div class="col-3">
                <app-welcome-header/>

                <div class="password-recovery-card shadow-sm p-4 mt-3 bg-white">
                    <div class="password-recovery-card-header">
                        <h5>{{"passwordRecovery.title" | translate}}</h5>
                        <p class="text-muted small mt-1">{{"passwordRecovery.description" | translate}}</p>
                    </div>
                    <div class="password-recovery-card-body">
                        <form [formGroup]="form">
                            <div class="form-group mt-4">
                                <label for="email">{{"passwordRecovery.email" | translate}}</label>
                                <input
                                    id="email"
                                    type="text"
                                    class="form-control mt-2"
                                    formControlName="email"
                                    placeholder="{{'passwordRecovery.emailPlaceholder' | translate}}"
                                />
                            </div>

                            <div>
                                <button class="btn btn-primary w-100 mt-4">
                                    {{"passwordRecovery.submit" | translate}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <app-small-footer/>
            </div>
        </div>
    `,
    styles: [`
        .password-recovery-card {
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
export class PasswordRecoveryComponent implements OnInit {
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
        });
    }
}
