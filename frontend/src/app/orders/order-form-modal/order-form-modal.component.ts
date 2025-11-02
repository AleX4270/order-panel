import { DatePipe } from '@angular/common';
import { Component, computed, ElementRef, inject, OnDestroy, Signal, signal, ViewChild, WritableSignal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { NgSelectComponent } from '@ng-select/ng-select';

@Component({
    selector: 'app-order-form-modal',
    imports: [ReactiveFormsModule, NgSelectComponent, DatePipe],
    providers: [DatePipe],
    template: `
        <div #modalRef class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary">Formularz zlecenia</h5>
                    </div>
                    <div class="modal-body">
                        @if(!isLoading() && form) {
                            <form [formGroup]="form" class="p-3">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="orderNumber">Numer zlecenia</label>
                                        <input
                                            type="text"
                                            formControlName="orderNumber"
                                            id="orderNumber"
                                            name="orderNumber"
                                            class="form-field input"
                                            [placeholder]="'Podaj numer zamówienia'"
                                        />
                                    </div>

                                    <div class="form-group col-6 mt-3">
                                        <label for="priorityId">Priorytet</label>
                                        <ng-select 
                                            formControlName="priorityId"
                                            [items]="[1, 2, 3]"
                                            [multiple]="false"
                                            [placeholder]="'Wybierz priorytet'"
                                            class="form-field dropdown"
                                        />
                                    </div>

                                    <div class="form-group col-6 mt-3">
                                        <label for="phoneNumber">Numer telefonu</label>
                                        <input
                                            type="tel"
                                            formControlName="phoneNumber"
                                            id="phoneNumber"
                                            name="phoneNumber"
                                            class="form-field input"
                                            [placeholder]="'Podaj numer telefonu'"
                                        />
                                    </div>
                                </div>

                                <div class="row mt-3 pt-2">
                                    <div class="form-group col-6">
                                        <label for="countryId">Kraj</label>
                                        <ng-select
                                            formControlName="countryId"
                                            [items]="[]"
                                            [multiple]="false"
                                            [placeholder]="'Wybierz kraj'"
                                            class="form-field dropdown"
                                        />
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="provinceId">Województwo</label>
                                        <ng-select
                                            formControlName="provinceId"
                                            [items]="[]"
                                            [multiple]="false"
                                            [placeholder]="'Wybierz województwo'"
                                            class="form-field dropdown"
                                        />
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="form-group col-6">
                                        <label for="cityId">Miasto</label>
                                        <ng-select 
                                            formControlName="cityId"
                                            [items]="[]"
                                            [multiple]="false"
                                            [placeholder]="'Wybierz miasto'"
                                            class="form-field dropdown"
                                        />
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="address">Adres</label>
                                        <input
                                            type="text"
                                            formControlName="address"
                                            id="address"
                                            name="address"
                                            class="form-field input"
                                            [placeholder]="'Podaj adres'"
                                        />
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="form-group col-6">
                                        <label for="dateCreation">Data utworzenia</label>
                                        <input
                                            type="date"
                                            formControlName="dateCreation"
                                            id="dateCreation"
                                            name="dateCreation"
                                            class="form-field input"
                                            [min]="currentDate()"
                                            (change)="onChangeDateCreation()"
                                        />
                                    </div>

                                    <div class="form-group col-6">
                                        <label for="dateDeadline">Termin realizacji</label>
                                        <input
                                            type="date"
                                            formControlName="dateDeadline"
                                            id="dateDeadline"
                                            name="dateDeadline"
                                            class="form-field input"
                                            [min]="form.get('dateCreation')?.value"
                                        />
                                    </div>

                                    @if(isEditScenario()) {
                                        <div class="form-group col-6 mt-3">
                                            <label for="dateCompleted">Data ukończenia</label>
                                            <input
                                                type="date"
                                                formControlName="dateCompleted"
                                                id="dateCompleted"
                                                name="dateCompleted"
                                                class="form-field input"
                                                [min]="form.get('dateCreation')?.value"
                                            />
                                        </div>
                                    }
                                </div>

                                <div class="row mt-3 pt-2">
                                    <div class="form-group col-12">
                                        <label for="remarks">Uwagi</label>
                                        <textarea
                                            id="remarks"
                                            name="remarks"
                                            rows="4"
                                            formControlName="remarks"
                                            class="form-field input mt-2"
                                            [placeholder]="'Uwagi dla zlecenia...'"
                                        ></textarea>
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
            orderNumber: [null, [Validators.required, Validators.maxLength(32)]],
            countryId: [null, Validators.required],
            provinceId: [null, Validators.required],
            cityId: [null, Validators.required],
            address: [null, [Validators.required, Validators.maxLength(255)]],
            phoneNumber: [null, [Validators.required, Validators.maxLength(32), Validators.pattern(/^(?:\+?\d{1,3}|\(?\d{2,4}\)?)?[\s-]?\d{3}(?:[\s-]?\d{2,3}){2,3}$/)]],
            priorityId: [null, Validators.required],
            dateCreation: [this.currentDate(), Validators.required],
            dateDeadline: [this.initialDateDeadline(), Validators.required],
            dateCompleted: [null],
            remarks: [null, [Validators.maxLength(2000)]]
        });
    }

    protected onChangeDateCreation(): void {
        let dateCreation = this.form.get('dateCreation')?.value;

        if(!dateCreation) return;

        const dateDeadlineField = this.form.get('dateDeadline');
        const dateCompletedField = this.form.get('dateCompleted');

        dateCreation = new Date(dateCreation);
        const dateDeadline = dateDeadlineField?.value ? new Date(dateDeadlineField.value) : null;
        const dateCompleted = dateCompletedField?.value ? new Date(dateCompletedField.value) : null;

        if(dateDeadline && dateDeadline < dateCreation) { 
            dateDeadlineField?.reset();
        }

        if(dateCompleted && dateCompleted < dateCreation) { 
            dateCompletedField?.reset();
        }
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
