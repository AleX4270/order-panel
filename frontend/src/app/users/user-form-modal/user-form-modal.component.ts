import { DatePipe } from '@angular/common';
import { Component, DestroyRef, ElementRef, inject, OnDestroy, output, signal, ViewChild, WritableSignal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { InputErrorLabelComponent } from '../../shared/components/input-error-label/input-error-label.component';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { ToastService } from '../../shared/services/toast/toast.service';
import { ToastType } from '../../shared/enums/enums';
import { UserItem, UserParams } from '../../shared/types/user.types';
import { UserService } from '../../shared/services/api/user/user.service';
import { validatePasswordStrength } from '../../shared/validators/password-strength.validator';
import { validatePasswordMatch } from '../../shared/validators/password-match.validator';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';

@Component({
    selector: 'app-user-form-modal',
    imports: [ReactiveFormsModule, InputErrorLabelComponent, TranslatePipe],
    providers: [DatePipe],
    template: `
        <div #modalRef class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary">{{ ("userForm." + (isEditScenario() ? "updateTitle" : "createTitle") | translate) + (isEditScenario() ? ' - #' + this.userId() : '') }}</h5>
                    </div>
                    <div class="modal-body">
                        @if(!isLoading() && form) {
                            <form [formGroup]="form" class="p-3">
                                <div class="row">
                                    <div class="form-group col-6">
                                        <label for="firstName">{{ "userForm.firstName" | translate }}</label>
                                        <input
                                            type="text"
                                            formControlName="firstName"
                                            id="firstName"
                                            name="firstName"
                                            class="form-field input"
                                            [placeholder]="'userForm.firstNamePlaceholder' | translate"
                                        />
                                        <app-input-error-label [control]="form.get('firstName')" />
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="lastName">{{ "userForm.lastName" | translate }}</label>
                                        <input
                                            type="text"
                                            formControlName="lastName"
                                            id="lastName"
                                            name="lastName"
                                            class="form-field input"
                                            [placeholder]="'userForm.lastNamePlaceholder' | translate"
                                        />
                                        <app-input-error-label [control]="form.get('lastName')" />
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="form-group col-6">
                                        <label for="username" class="required">{{ "userForm.username" | translate }}</label>
                                        <input
                                            type="text"
                                            formControlName="username"
                                            id="username"
                                            name="username"
                                            class="form-field input"
                                            [placeholder]="'userForm.usernamePlaceholder' | translate"
                                        />
                                        <app-input-error-label [control]="form.get('username')" />
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="email" class="required">{{ "userForm.email" | translate }}</label>
                                        <input
                                            type="email"
                                            formControlName="email"
                                            id="email"
                                            name="email"
                                            class="form-field input"
                                            [placeholder]="'userForm.emailPlaceholder' | translate"
                                        />
                                        <app-input-error-label [control]="form.get('email')" />
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="form-group col-6">
                                        <label for="password">{{ "userForm.password" | translate }}</label>
                                        <input
                                            type="password"
                                            formControlName="password"
                                            id="password"
                                            name="password"
                                            class="form-field input"
                                            [placeholder]="'userForm.passwordPlaceholder' | translate"
                                        />
                                        <app-input-error-label [control]="form.get('password')" />
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="passwordConfirmed">{{ "userForm.passwordConfirmed" | translate }}</label>
                                        <input
                                            type="password"
                                            formControlName="passwordConfirmed"
                                            id="passwordConfirmed"
                                            name="passwordConfirmed"
                                            class="form-field input"
                                            [placeholder]="'userForm.passwordConfirmedPlaceholder' | translate"
                                        />
                                        <app-input-error-label [control]="form.get('passwordConfirmed')" />
                                    </div>
                                </div>
                            </form>
                        }
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-danger" (click)="closeModal()">{{"basic.cancel" | translate}}</button>
                        <button type="button" class="btn btn-sm btn-primary" (click)="saveUser()">{{"basic.save" | translate}}</button>
                    </div>
                </div>
            </div>
        </div>
    `,
    styles: [`
        label {
            font-size: var(--font-size-sm);
        }

        input, ng-select {
            box-shadow: var(--shadow-xs);
            margin-top: 5px;
        }    
    `],
})
export class UserFormModalComponent implements OnDestroy {
    @ViewChild('modalRef') userFormModal!: ElementRef;

    private readonly translateService: TranslateService = inject(TranslateService);
    private readonly toastService: ToastService = inject(ToastService);
    private readonly formBuilder: FormBuilder = inject(FormBuilder);
    private readonly userService: UserService = inject(UserService);
    private readonly destroyRef: DestroyRef = inject(DestroyRef);

    private modal?: any;

    protected form!: FormGroup;
    protected userId: WritableSignal<number | null> = signal<number | null>(null);
    protected isEditScenario: WritableSignal<boolean> = signal<boolean>(false);
    protected isLoading: WritableSignal<boolean> = signal<boolean>(false);

    protected userSaved = output<void>();

    public showForm(userId?: number): void {
        this.isLoading.set(true);

        if(userId) {
            this.userId.set(userId);
            this.isEditScenario.set(true);
            this.loadDetails(userId);
        }

        this.initForm();
        this.openModal();        
    }
    
    protected openModal(): void {
        if(!this.modal) {
            this.modal = new window.bootstrap.Modal(this.userFormModal.nativeElement, {
                focus: true,
                keyboard: false,
                backdrop: 'static'
            });
        }

        if(typeof window !== 'undefined' && window.bootstrap) {
            this.modal.show();
        }

        this.isLoading.set(false);
    }

    protected closeModal(): void {
        this.isEditScenario.set(false);
        this.userId.set(null);
        this.form.reset();
        this.modal?.hide();
    }

    private initForm(): void {
        this.form = this.formBuilder.group({
            id: [null],
            firstName: [null],
            lastName: [null],
            username: [null, Validators.required],
            email: [null, [Validators.required, Validators.pattern(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)]],
            password: [null, [validatePasswordStrength()]],
            passwordConfirmed: [null, [validatePasswordMatch()]],
        });

        this.registerFormChanges();
    }

    private registerFormChanges(): void {
        const password = this.form.get('password');

        if(!password) {
            return;
        }

        password.valueChanges.pipe(
            takeUntilDestroyed(this.destroyRef),
        )
        .subscribe({
            next: () => {
                this.form.get('passwordConfirmed')?.updateValueAndValidity();
            }
        });
    }

    private loadDetails(userId: number): void {
        this.userService.show(userId).subscribe({
            next: (res) => {
                const user: UserItem | null = res.data;
                
                if(!user) {
                    return;
                }

                this.form.patchValue({
                    id: user.id,
                    firstName: user.firstName,
                    lastName: user.lastName,
                    username: user.name,
                    email: user.email,
                });
            },
            error: (err) => {
                console.error(err);
            }
        });
    }

    protected saveUser(): void {
        if(this.form.invalid) {
            this.form.markAllAsDirty();
            this.toastService.show(this.translateService.instant('form.error'), ToastType.danger);
            return;
        }

        const formValues = this.form.value;

        let userParams = {
            username: formValues.username,
            email: formValues.email,
        } as UserParams;

        if(formValues.id) {
            userParams.id = formValues.id;
        }

        if(formValues.firstName) {
            userParams.firstName = formValues.firstName;
        }

        if(formValues.lastName) {
            userParams.lastName = formValues.lastName;
        }

        if(formValues.password) {
            userParams.password = formValues.password;
        }

        if(formValues.passwordConfirmed) {
            userParams.passwordConfirmed = formValues.passwordConfirmed;
        }

        const method = this.isEditScenario()
            ? this.userService.update(userParams)
            : this.userService.store(userParams);

        method.subscribe({
            next: (res) => {
                this.toastService.show(
                    this.translateService.instant('userForm.saveSuccessMessage'),
                    ToastType.success,
                );
                this.userSaved.emit();
            },
            error: (err) => {
                console.log(err);
                this.toastService.show(
                    this.translateService.instant('userForm.saveErrorMessage'),
                    ToastType.danger,
                );
            },
            complete: () => {
                this.closeModal();
            }
        });
    }

    ngOnDestroy(): void {
        this.modal?.dispose();
    }
}
