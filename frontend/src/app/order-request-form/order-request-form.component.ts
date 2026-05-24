import { Component, inject, signal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { InputErrorLabelComponent } from '../shared/components/input-error-label/input-error-label.component';
import { ButtonComponent } from '../shared/components/button/button.component';
import { SmallFooterComponent } from '../shared/components/small-footer/small-footer.component';
import { AddressSubformComponent } from '../shared/components/address-subform/address-subform.component';
import { ToastService } from '../shared/services/toast/toast.service';
import { ToastType } from '../shared/enums/enums';
import { OrderRequestService } from '../shared/services/api/order-request/order-request.service';
import { OrderRequestParams } from '../shared/types/order-request.types';
import { finalize } from 'rxjs';

@Component({
    selector: 'app-order-request-form',
    imports: [
        ReactiveFormsModule,
        TranslatePipe,
        InputErrorLabelComponent,
        ButtonComponent,
        SmallFooterComponent,
        AddressSubformComponent,
    ],
    template: `
        <div class="min-h-screen flex items-center justify-center">
            <div class="w-full md:max-w-5xl">
                <div class="w-full flex justify-center pb-5">
                    <h1 class="text-primary font-bold text-3xl">{{"orderRequestForm.title" | translate}}</h1>
                </div>

                <div class="card bg-base-100 sm:p-1 sm:shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-xl">{{ "orderRequestForm.contactData" | translate }}</h5>
                        <p class="text-base-content/50 mt-1">
                            {{ "orderRequestForm.description" | translate }}
                        </p>

                        <form [formGroup]="form" (ngSubmit)="save()" class="[&_label]:mb-2">
                            <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap md:mt-4">
                                <div class="flex flex-col w-full md:flex-1">
                                    <label for="firstName" class="label field-required">{{ "orderRequestForm.firstName" | translate }}</label>
                                    <input
                                        type="text"
                                        formControlName="firstName"
                                        id="firstName"
                                        name="firstName"
                                        class="input text-xs w-full"
                                        [placeholder]="'orderRequestForm.firstNamePlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('firstName')" />
                                </div>

                                <div class="flex flex-col w-full md:flex-1">
                                    <label for="lastName" class="label field-required">{{ "orderRequestForm.lastName" | translate }}</label>
                                    <input
                                        type="text"
                                        formControlName="lastName"
                                        id="lastName"
                                        name="lastName"
                                        class="input text-xs w-full"
                                        [placeholder]="'orderRequestForm.lastNamePlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('lastName')" />
                                </div>
                            </div>

                            <div class="w-full flex flex-col items-center gap-y-3 mt-3 md:flex-row md:gap-3 md:flex-wrap md:mt-4">
                                <div class="flex flex-col w-full md:flex-1">
                                    <label for="email" class="label field-required">{{ "orderRequestForm.email" | translate }}</label>
                                    <input
                                        type="email"
                                        formControlName="email"
                                        id="email"
                                        name="email"
                                        class="input text-xs w-full"
                                        [placeholder]="'orderRequestForm.emailPlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('email')" />
                                </div>

                                <div class="flex flex-col w-full md:flex-1">
                                    <label for="phoneNumber" class="label field-required">{{ "orderRequestForm.phoneNumber" | translate }}</label>
                                    <input
                                        type="tel"
                                        formControlName="phoneNumber"
                                        id="phoneNumber"
                                        name="phoneNumber"
                                        class="input text-xs w-full"
                                        [placeholder]="'orderRequestForm.phoneNumberPlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('phoneNumber')" />
                                </div>
                            </div>

                            <app-address-subform [form]="form" [setDefaultCountry]="true" [allowCitySelection]="false"/>

                            <div class="w-full flex flex-col items-center gap-y-3 mt-3 md:flex-row md:gap-3 md:flex-wrap md:mt-4">
                                <div class="flex flex-col w-full">
                                    <label for="remarks" class="label">{{ "orderRequestForm.remarks" | translate }}</label>
                                    <textarea
                                        id="remarks"
                                        name="remarks"
                                        rows="4"
                                        formControlName="remarks"
                                        class="textarea mt-2 text-xs w-full"
                                        [placeholder]="'orderRequestForm.remarksPlaceholder' | translate"
                                    ></textarea>
                                    <app-input-error-label [control]="form.get('remarks')" />
                                </div>
                            </div>

                            <div class="w-full flex flex-col items-center gap-y-3 mt-5 md:flex-row md:gap-3 md:flex-wrap">
                                <div class="flex gap-1 w-full">
                                    <input
                                        type="checkbox"
                                        formControlName="isConsentGiven"
                                        id="isConsentGiven"
                                        name="isConsentGiven"
                                        class="toggle toggle-sm me-1"
                                    /> 
                                    <label for="isConsentGiven" class="text-wrap label field-required">{{ "orderRequestForm.consentMessage" | translate }}</label>
                                    <app-input-error-label [control]="form.get('isConsentGiven')" />
                                </div>
                            </div>

                            <div class="card-actions w-full">
                                <app-button
                                    class="w-full"
                                    classList="w-full btn btn-primary mt-10"
                                    type="submit"
                                    [isDisabled]="!form.get('isConsentGiven')?.value"
                                    [isLoading]="isSubmitted()"
                                >{{'orderRequestForm.submit' | translate}}</app-button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-3 mb-3 md:mb-0">
                    <app-small-footer />
                </div>
            </div>
        </div>
    `,
    styles: [``],
})
export class OrderRequestFormComponent {
    private formBuilder = inject(FormBuilder);
    private toastService = inject(ToastService);
    private translateService = inject(TranslateService);
    private orderRequestService = inject(OrderRequestService);

    protected form: FormGroup = this.formBuilder.group({
        firstName: [null, [Validators.required, Validators.maxLength(128)]],
        lastName: [null, [Validators.required, Validators.maxLength(128)]],
        alias: [null, [Validators.maxLength(128)]],
        email: [null, [Validators.required, Validators.maxLength(255), Validators.email]],
        phoneNumber: [null, [Validators.required, Validators.maxLength(32), Validators.pattern(/^[0-9\s()+-]{6,20}$/)]],
        countryId: [null, [Validators.required, Validators.min(1)]],
        provinceId: [null, [Validators.required, Validators.min(1)]],
        city: [null, [Validators.required, Validators.maxLength(128)]],
        postalCode: [null, [Validators.required, Validators.maxLength(32)]],
        address: [null, [Validators.required, Validators.maxLength(255)]],
        remarks: [null, [Validators.maxLength(2000)]],
        isConsentGiven: [null, Validators.requiredTrue],
    });

    protected isSubmitted = signal<boolean>(false);

    public save(): void {
        if(this.isSubmitted()) {
            return;
        }

        if(this.form.invalid) {
            this.form.markAllAsDirty();
            this.toastService.show(this.translateService.instant('form.error'), ToastType.danger);
            return;
        }

        this.isSubmitted.set(true);
        const formValues = this.form.value;

        let params: OrderRequestParams = {
            firstName: formValues.firstName,
            lastName: formValues.lastName,
            email: formValues.email,
            phoneNumber: formValues.phoneNumber,
            countryId: formValues.countryId,
            provinceId: formValues.provinceId,
            city: formValues.city,
            postalCode: formValues.postalCode,
            address: formValues.address,
            isConsentGiven: formValues.isConsentGiven,
        };

        if(formValues.alias) {
            params.alias = formValues.alias;
        }

        if(formValues.remarks) {
            params.remarks = formValues.remarks;
        }

        this.orderRequestService.store(params)
        .pipe(
            finalize(() => this.isSubmitted.set(false))
        )
        .subscribe({
            next: () => {
                this.toastService.show(
                    this.translateService.instant('orderRequestForm.saveSuccessMessage'),
                    ToastType.success,
                );
            },
            error: (err) => {
                console.error(err);
                this.toastService.show(err.error.message, ToastType.danger);
            },
        });
    }
}
