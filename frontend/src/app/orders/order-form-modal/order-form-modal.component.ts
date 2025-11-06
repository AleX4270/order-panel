import { DatePipe } from '@angular/common';
import { Component, computed, ElementRef, inject, OnDestroy, Signal, signal, ViewChild, WritableSignal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { NgSelectComponent } from '@ng-select/ng-select';
import { InputErrorLabelComponent } from '../../shared/components/input-error-label/input-error-label.component';
import { TranslatePipe } from '@ngx-translate/core';
import { validateOrderDateRange } from '../../shared/validators/order-date-range.validator';

@Component({
    selector: 'app-order-form-modal',
    imports: [ReactiveFormsModule, NgSelectComponent, DatePipe, InputErrorLabelComponent, TranslatePipe],
    providers: [DatePipe],
    template: `
        <div #modalRef class="modal modal-lg fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary">{{ "orderForm.title" | translate }}</h5>
                    </div>
                    <div class="modal-body">
                        @if(!isLoading() && form) {
                            <form [formGroup]="form" class="p-3">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="orderNumber" class="required">{{ "orderForm.orderNumber" | translate }}</label>
                                        <input
                                            type="text"
                                            formControlName="orderNumber"
                                            id="orderNumber"
                                            name="orderNumber"
                                            class="form-field input required"
                                            [placeholder]="'orderForm.orderNumberPlaceholder' | translate"
                                        />
                                    </div>
                                    <app-input-error-label [control]="form.get('orderNumber')" />
                                </div>

                                <div class="row mt-4">
                                    <div class="form-group col-3">
                                        <label for="countryId" class="required">{{ "orderForm.country" | translate }}</label>
                                        <ng-select
                                            formControlName="countryId"
                                            [items]="[]"
                                            [multiple]="false"
                                            [placeholder]="'orderForm.countryPlaceholder' | translate"
                                            class="form-field dropdown"
                                        />
                                        <app-input-error-label [control]="form.get('countryId')" />
                                    </div>

                                    <div class="form-group col-5">
                                        <label for="provinceId" class="required">{{ "orderForm.province" | translate }}</label>
                                        <ng-select
                                            formControlName="provinceId"
                                            [items]="[]"
                                            [multiple]="false"
                                            [placeholder]="'orderForm.provincePlaceholder' | translate"
                                            class="form-field dropdown"
                                        />
                                        <app-input-error-label [control]="form.get('provinceId')" />
                                    </div>

                                    <div class="form-group col-4">
                                        <label for="cityId" class="required">{{ "orderForm.city" | translate }}</label>
                                        <ng-select 
                                            formControlName="cityId"
                                            [items]="[]"
                                            [multiple]="false"
                                            [placeholder]="'orderForm.cityPlaceholder' | translate"
                                            class="form-field dropdown"
                                        />
                                        <app-input-error-label [control]="form.get('cityId')" />
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="form-group col-3">
                                        <label for="postalCode">{{ "orderForm.postalCode" | translate }}</label>
                                        <input
                                            type="text"
                                            formControlName="postalCode"
                                            id="postalCode"
                                            name="postalCode"
                                            class="form-field input"
                                            [placeholder]="'orderForm.postalCodePlaceholder' | translate"
                                        />
                                        <app-input-error-label [control]="form.get('postalCode')" />
                                    </div>

                                    <div class="form-group col-9">
                                        <label for="address" class="required">{{ "orderForm.address" | translate }}</label>
                                        <input
                                            type="text"
                                            formControlName="address"
                                            id="address"
                                            name="address"
                                            class="form-field input"
                                            [placeholder]="'orderForm.addressPlaceholder' | translate"
                                        />
                                        <app-input-error-label [control]="form.get('address')" />
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="form-group col-6">
                                        <label for="priorityId" class="required">{{ "orderForm.priority" | translate }}</label>
                                        <ng-select 
                                            formControlName="priorityId"
                                            [items]="[1, 2, 3]"
                                            [multiple]="false"
                                            [placeholder]="'orderForm.priorityPlaceholder' | translate"
                                            class="form-field dropdown"
                                        />
                                        <app-input-error-label [control]="form.get('priorityId')" />
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="phoneNumber" class="required">{{ "orderForm.phoneNumber" | translate }}</label>
                                        <input
                                            type="tel"
                                            formControlName="phoneNumber"
                                            id="phoneNumber"
                                            name="phoneNumber"
                                            class="form-field input"
                                            [placeholder]="'orderForm.phoneNumberPlaceholder' | translate"
                                        />
                                        <app-input-error-label [control]="form.get('phoneNumber')" />
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="form-group col-4">
                                        <label for="dateCreation" class="required">{{ "orderForm.dateCreation" | translate }}</label>
                                        <input
                                            type="date"
                                            formControlName="dateCreation"
                                            id="dateCreation"
                                            name="dateCreation"
                                            class="form-field input"
                                            [min]="currentDate()"
                                        />
                                        <app-input-error-label [control]="form.get('dateCreation')" />
                                    </div>

                                    <div class="form-group col-4">
                                        <label for="dateDeadline" class="required">{{ "orderForm.dateDeadline" | translate }}</label>
                                        <input
                                            type="date"
                                            formControlName="dateDeadline"
                                            id="dateDeadline"
                                            name="dateDeadline"
                                            class="form-field input"
                                            [min]="form.get('dateCreation')?.value"
                                        />
                                        <app-input-error-label [control]="form.get('dateDeadline')" />
                                    </div>

                                    @if(isEditScenario()) {
                                        <div class="form-group col-4">
                                            <label for="dateCompleted">{{ "orderForm.dateCompleted" | translate }}</label>
                                            <input
                                                type="date"
                                                formControlName="dateCompleted"
                                                id="dateCompleted"
                                                name="dateCompleted"
                                                class="form-field input"
                                                [min]="form.get('dateCreation')?.value"
                                            />
                                            <app-input-error-label [control]="form.get('dateCompleted')" />
                                        </div>
                                    }
                                </div>

                                <div class="row mt-4">
                                    <div class="form-group col-12">
                                        <label for="remarks">{{ "orderForm.remarks" | translate }}</label>
                                        <textarea
                                            id="remarks"
                                            name="remarks"
                                            rows="4"
                                            formControlName="remarks"
                                            class="form-field input mt-2"
                                            [placeholder]="'orderForm.remarksPlaceholder' | translate"
                                        ></textarea>
                                        <app-input-error-label [control]="form.get('remarks')" />
                                    </div>
                                </div>
                            </form>
                        }
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Anuluj</button>
                        <button type="button" class="btn btn-sm btn-primary">Zapisz</button>
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
export class OrderFormModalComponent implements OnDestroy {
    @ViewChild('modalRef') orderFormModal!: ElementRef;

    private readonly formBuilder: FormBuilder = inject(FormBuilder);
    private readonly datePipe: DatePipe = inject(DatePipe);

    private modal?: any;

    protected form!: FormGroup;
    protected orderId: WritableSignal<number | null> = signal<number | null>(null);
    protected isEditScenario: WritableSignal<boolean> = signal<boolean>(true);
    protected isLoading: WritableSignal<boolean> = signal<boolean>(false);

    protected currentDate: Signal<string | null> = computed(() => {
        return this.datePipe.transform(new Date().toString(), 'yyyy-MM-dd');
    });

    protected initialDateDeadline: Signal<string | null> = computed(() => {
        const date = new Date();
        date.setDate(date.getDate() + 14);
        return this.datePipe.transform(date.toString(), 'yyyy-MM-dd');
    });

    private initForm(): void {
        this.form = this.formBuilder.group({
            orderNumber: [null, [Validators.required, Validators.maxLength(32), Validators.pattern(/^[0-9]+\/[0-9]{4}$/)]],
            countryId: [null, Validators.required],
            provinceId: [null, Validators.required],
            cityId: [null, Validators.required],
            postalCode: [null, Validators.maxLength(32)],
            address: [null, [Validators.required, Validators.maxLength(255)]],
            phoneNumber: [null, [Validators.required, Validators.maxLength(32), Validators.pattern(/^[0-9\s()+-]{6,20}$/)]],
            priorityId: [null, Validators.required],
            dateCreation: [this.currentDate(), Validators.required],
            dateDeadline: [this.initialDateDeadline(), Validators.required],
            dateCompleted: [null],
            remarks: [null, [Validators.maxLength(2000)]]
        },{
            validators: [validateOrderDateRange()],
        });
    }

    public showForm(id?: number): void {
        this.isLoading.set(true);

        if(id) {
            this.orderId.set(id);
            this.isEditScenario.set(true);
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
        this.form.reset();
        this.modal?.hide();
    }

    ngOnDestroy(): void {
        this.modal?.dispose();
    }
}
