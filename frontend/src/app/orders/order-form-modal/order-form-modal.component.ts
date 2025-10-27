import { Component, ElementRef, inject, OnDestroy, signal, ViewChild, WritableSignal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { NgSelectComponent } from '@ng-select/ng-select';

@Component({
    selector: 'app-order-form-modal',
    imports: [ReactiveFormsModule, NgSelectComponent],
    template: `
        <div #modalRef class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Formularz zlecenia</h5>
                    </div>
                    <div class="modal-body">
                        @if(!isLoading() && form) {
                            <form [formGroup]="form" class="p-3">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="orderNumber" class="fw-semibold">Numer zlecenia</label>
                                        <input
                                            [disabled]="true"
                                            type="text"
                                            formControlName="orderNumber"
                                            id="orderNumber"
                                            name="orderNumber"
                                            class="form-control form-control-sm"
                                        />
                                    </div>

                                    <div class="form-group col-6 mt-2">
                                        <label for="priorityId" class="fw-semibold">Priorytet</label>
                                        <ng-select 
                                            formControlName="priorityId"
                                            [items]="[]"
                                            [multiple]="false"
                                            [placeholder]="'Wybierz priorytet'"
                                            class="form-control"
                                        />
                                    </div>
                                </div>

                                <div class="row mt-3 pt-2">
                                    <div class="form-group col-6">
                                        <label for="countryId" class="fw-semibold">Kraj</label>
                                        <ng-select
                                            formControlName="countryId"
                                            [items]="[]"
                                            [multiple]="false"
                                            [placeholder]="'Wybierz kraj'"
                                            class="form-control"
                                        />
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="provinceId" class="fw-semibold">Województwo</label>
                                        <ng-select
                                            formControlName="provinceId"
                                            [items]="[]"
                                            [multiple]="false"
                                            [placeholder]="'Wybierz województwo'"
                                            class="form-control"
                                        />
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="form-group col-6">
                                        <label for="cityId" class="fw-semibold">Miasto</label>
                                        <ng-select 
                                            formControlName="cityId"
                                            [items]="[]"
                                            [multiple]="false"
                                            [placeholder]="'Wybierz miasto'"
                                            class="form-control"
                                        />
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="address" class="fw-semibold">Adres</label>
                                        <input
                                            [disabled]="true"
                                            type="text"
                                            formControlName="address"
                                            id="address"
                                            name="address"
                                            class="form-control form-control-sm"
                                        />
                                    </div>
                                </div>

                                <div class="row mt-3 pt-2">
                                    <div class="form-group col-6">
                                        <label for="phoneNumber" class="fw-semibold">Numer telefonu</label>
                                        <input
                                            [disabled]="true"
                                            type="tel"
                                            formControlName="phoneNumber"
                                            id="phoneNumber"
                                            name="phoneNumber"
                                            class="form-control form-control-sm"
                                        />
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
            font-size: 0.9rem;
        }

        input, ng-select {
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            margin-top: 5px;
        }    
    `],
})
export class OrderFormModalComponent implements OnDestroy {
    @ViewChild('modalRef') orderFormModal!: ElementRef;

    private readonly formBuilder: FormBuilder = inject(FormBuilder);

    private modal?: any;

    protected form!: FormGroup;
    protected orderId: WritableSignal<number | null> = signal<number | null>(null);
    protected isEditScenario: WritableSignal<boolean> = signal<boolean>(false);
    protected isLoading: WritableSignal<boolean> = signal<boolean>(false);

    private initForm(): void {
        this.form = this.formBuilder.group({
            orderNumber: [null, Validators.required],
            countryId: [null, Validators.required],
            provinceId: [null, Validators.required],
            cityId: [null, Validators.required],
            address: [null, Validators.required],
            phoneNumber: [null, Validators.required],
            priorityId: [null, Validators.required],
            dateCreation: [null, Validators.required],
            dateDeadline: [null, Validators.required],
            remarks: [null]
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
