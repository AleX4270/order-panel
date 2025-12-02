import { DatePipe, NgClass } from '@angular/common';
import { Component, computed, DestroyRef, ElementRef, inject, OnDestroy, output, Signal, signal, ViewChild, WritableSignal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { NgSelectComponent } from '@ng-select/ng-select';
import { InputErrorLabelComponent } from '../../shared/components/input-error-label/input-error-label.component';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { validateOrderDateRange } from '../../shared/validators/order-date-range.validator';
import { PriorityItem } from '../../shared/types/priority.types';
import { StatusItem } from '../../shared/types/status.types';
import { CountryItem } from '../../shared/types/country.types';
import { ProvinceItem } from '../../shared/types/province.types';
import { CityItem } from '../../shared/types/city.types';
import { PriorityService } from '../../shared/services/api/priority/priority.service';
import { StatusService } from '../../shared/services/api/status/status.service';
import { CountryService } from '../../shared/services/api/country/country.service';
import { catchError, count, distinctUntilChanged, forkJoin, map, tap } from 'rxjs';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { DEFAULT_COUNTRY_SYMBOL, DEFAULT_PRIORITY_SYMBOL, DEFAULT_STATUS_SYMBOL } from '../../app.constants';
import { ProvinceService } from '../../shared/services/api/province/province.service';
import { CityService } from '../../shared/services/api/city/city.service';
import { ToastService } from '../../shared/services/toast/toast.service';
import { ToastType } from '../../shared/enums/enums';
import { OrderService } from '../../shared/services/api/order/order.service';
import { OrderItem, OrderParams } from '../../shared/types/order.types';
import { UserParams } from '../../shared/types/user.types';
import { UserService } from '../../shared/services/api/user/user.service';

@Component({
    selector: 'app-user-form-modal',
    imports: [ReactiveFormsModule, NgSelectComponent, DatePipe, InputErrorLabelComponent, TranslatePipe, NgClass],
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
    @ViewChild('modalRef') orderFormModal!: ElementRef;

    private readonly translateService: TranslateService = inject(TranslateService);
    private readonly toastService: ToastService = inject(ToastService);
    private readonly destroyRef: DestroyRef = inject(DestroyRef);
    private readonly formBuilder: FormBuilder = inject(FormBuilder);
    private readonly datePipe: DatePipe = inject(DatePipe);
    private readonly userService: UserService = inject(UserService);

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
            this.modal = new window.bootstrap.Modal(this.orderFormModal.nativeElement, {
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
            email: [null, Validators.required], //TODO: Add the custom email validator
            //TODO: Add the custom password validator
            password: [null],
            passwordConfirmed: [null],
        });
    }

    private loadDetails(userId: number): void {
        // this.orderService.show(userId).subscribe({
        //     next: (res) => {
        //         const order: OrderItem | null = res.data;
                
        //         if(!order) {
        //             return;
        //         }

        //         console.log(this.priorities());
        //         console.log(this.statuses());

        //         this.form.patchValue({
        //             id: order.id,
        //             orderNumber: order.id,
        //             countryId: order.countryId,
        //             provinceId: order.provinceId,
        //             cityId: order.cityId,
        //             postalCode: order.postalCode,
        //             address: order.address,
        //             phoneNumber: order.phoneNumber,
        //             priorityId: order.priorityId,
        //             statusId: order.statusId,
        //             dateCreation: order.dateCreated,
        //             dateDeadline: order.dateDeadline,
        //             dateCompleted: order.dateCompleted ?? null,
        //             remarks: order.remarks
        //         });
        //     },
        //     error: (err) => {
        //         console.error(err);
        //     }
        // });
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
