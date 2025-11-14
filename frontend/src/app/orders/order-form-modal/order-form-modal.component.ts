import { DatePipe, NgClass } from '@angular/common';
import { Component, computed, DestroyRef, ElementRef, inject, OnDestroy, Signal, signal, ViewChild, WritableSignal } from '@angular/core';
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
import { catchError, count, forkJoin, map, of, takeUntil, tap } from 'rxjs';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { DEFAULT_COUNTRY_SYMBOL, DEFAULT_PRIORITY_SYMBOL, DEFAULT_STATUS_SYMBOL } from '../../app.constants';
import { ProvinceService } from '../../shared/services/api/province/province.service';
import { CityService } from '../../shared/services/api/city/city.service';
import { ToastService } from '../../shared/services/toast/toast.service';
import { ToastType } from '../../shared/enums/enums';

@Component({
    selector: 'app-order-form-modal',
    imports: [ReactiveFormsModule, NgSelectComponent, DatePipe, InputErrorLabelComponent, TranslatePipe, NgClass],
    providers: [DatePipe],
    template: `
        <div #modalRef class="modal modal-lg fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary">{{ "orderForm." + (isEditScenario() ? "updateTitle" : "createTitle") | translate }}</h5>
                    </div>
                    <div class="modal-body">
                        @if(!isLoading() && form) {
                            <form [formGroup]="form" class="p-3">
                                @if(isEditScenario()) {
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
                                }
                                
                                <div
                                    [ngClass]="{
                                        'row': true,
                                        'mt-4': isEditScenario()
                                    }"
                                >
                                    <div class="form-group col-3">
                                        <label for="countryId" class="required">{{ "orderForm.country" | translate }}</label>
                                        <ng-select
                                            formControlName="countryId"
                                            [items]="countries()"
                                            bindValue="id"
                                            bindLabel="name"
                                            [multiple]="false"
                                            [placeholder]="'orderForm.countryPlaceholder' | translate"
                                            class="form-field dropdown"
                                            (change)="onCountryChange($event)"
                                        />
                                        <app-input-error-label [control]="form.get('countryId')" />
                                    </div>

                                    <div class="form-group col-5">
                                        <label for="provinceId" class="required">{{ "orderForm.province" | translate }}</label>
                                        <ng-select
                                            formControlName="provinceId"
                                            [items]="provinces()"
                                            bindValue="id"
                                            bindLabel="name"
                                            [multiple]="false"
                                            [placeholder]="'orderForm.provincePlaceholder' | translate"
                                            class="form-field dropdown"
                                            (change)="onProvinceChange($event)"
                                        />
                                        <app-input-error-label [control]="form.get('provinceId')" />
                                    </div>

                                    <div class="form-group col-4">
                                        <label for="cityId" class="required">{{ "orderForm.city" | translate }}</label>
                                        <ng-select 
                                            formControlName="cityId"
                                            [items]="cities()"
                                            bindValue="id"
                                            bindLabel="name"
                                            [multiple]="false"
                                            [placeholder]="'orderForm.cityPlaceholder' | translate"
                                            class="form-field dropdown"
                                            addTagText="Dodaj miasto"
                                            [addTag]="addNewCity"
                                            (change)="onCityChange($event)"
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
                                    <div class="form-group col-3">
                                        <label for="priorityId" class="required">{{ "orderForm.priority" | translate }}</label>
                                        <ng-select 
                                            formControlName="priorityId"
                                            [items]="priorities()"
                                            bindValue="id"
                                            bindLabel="name"
                                            [multiple]="false"
                                            [placeholder]="'orderForm.priorityPlaceholder' | translate"
                                            class="form-field dropdown"
                                        />
                                        <app-input-error-label [control]="form.get('priorityId')" />
                                    </div>

                                    <div class="form-group col-3">
                                        <label for="statusId" class="required">{{ "orderForm.status" | translate }}</label>
                                        <ng-select 
                                            formControlName="statusId"
                                            [items]="statuses()"
                                            bindValue="id"
                                            bindLabel="name"
                                            [multiple]="false"
                                            [placeholder]="'orderForm.statusPlaceholder' | translate"
                                            class="form-field dropdown"
                                        />
                                        <app-input-error-label [control]="form.get('statusId')" />
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
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">{{"basic.cancel" | translate}}</button>
                        <button type="button" class="btn btn-sm btn-primary" (click)="saveOrder()">{{"basic.save" | translate}}</button>
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

    private readonly translateService: TranslateService = inject(TranslateService);
    private readonly toastService: ToastService = inject(ToastService);
    private readonly destroyRef: DestroyRef = inject(DestroyRef);
    private readonly formBuilder: FormBuilder = inject(FormBuilder);
    private readonly datePipe: DatePipe = inject(DatePipe);
    private readonly priorityService: PriorityService = inject(PriorityService);
    private readonly statusService: StatusService = inject(StatusService);
    private readonly countryService: CountryService = inject(CountryService);
    private readonly provinceService: ProvinceService = inject(ProvinceService);
    private readonly cityService: CityService = inject(CityService);

    private modal?: any;

    protected form!: FormGroup;
    protected orderId: WritableSignal<number | null> = signal<number | null>(null);
    protected isEditScenario: WritableSignal<boolean> = signal<boolean>(false);
    protected isLoading: WritableSignal<boolean> = signal<boolean>(false);

    protected priorities: WritableSignal<PriorityItem[]> = signal<PriorityItem[]>([]);
    protected statuses: WritableSignal<StatusItem[]> = signal<StatusItem[]>([]);
    protected countries: WritableSignal<CountryItem[]> = signal<CountryItem[]>([]);
    protected provinces: WritableSignal<ProvinceItem[]> = signal<ProvinceItem[]>([]);
    protected cities: WritableSignal<CityItem[]> = signal<CityItem[]>([]);

    protected onCityChange(event : any): void {
        console.log(event);
        console.log(this.cities());
    }

    protected addNewCity = (name: string): CityItem | null => {
        const cityName = name.trim();
        if(!cityName) {
            return null;
        }

        const isCityExisting = this.cities().some((city) => city.name.toLowerCase() == cityName.toLowerCase());
        if(isCityExisting) {
            return null;
        }

        const newCity = { id: 0, name: cityName} as CityItem;
        this.cities.set([...this.cities(), newCity]);

        return newCity;
    };

    protected currentDate: Signal<string | null> = computed(() => {
        return this.datePipe.transform(new Date().toString(), 'yyyy-MM-dd');
    });

    protected initialDateDeadline: Signal<string | null> = computed(() => {
        const date = new Date();
        date.setDate(date.getDate() + 14);
        return this.datePipe.transform(date.toString(), 'yyyy-MM-dd');
    });

    public showForm(id?: number): void {
        this.isLoading.set(true);

        if(id) {
            this.orderId.set(id);
            this.isEditScenario.set(true);
        }

        this.initForm();
        this.loadFormData();
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

    private initForm(): void {
        this.form = this.formBuilder.group({
            orderNumber: [{ value: null, disabled: true }],
            countryId: [null, Validators.required],
            provinceId: [null, Validators.required],
            cityId: [null, Validators.required],
            postalCode: [null, Validators.maxLength(32)],
            address: [null, [Validators.required, Validators.maxLength(255)]],
            phoneNumber: [null, [Validators.required, Validators.maxLength(32), Validators.pattern(/^[0-9\s()+-]{6,20}$/)]],
            priorityId: [null, Validators.required],
            statusId: [null, Validators.required],
            dateCreation: [this.currentDate(), Validators.required],
            dateDeadline: [this.initialDateDeadline(), Validators.required],
            dateCompleted: [null],
            remarks: [null, [Validators.maxLength(2000)]]
        },{
            validators: [validateOrderDateRange()],
        });
    }

    private loadFormData(): void {
        forkJoin({
            priorities: this.priorityService.index(),
            statuses: this.statusService.index(),
            countries: this.countryService.index(),
        })
        .pipe(
            map(({priorities, statuses, countries}) => ({
                priorities: priorities.data?.items ?? [],
                statuses: statuses.data?.items ?? [],
                countries: countries.data?.items ?? [],
            })),
            tap(({priorities, statuses, countries}) => {
                this.priorities.set(priorities);
                this.statuses.set(statuses);
                this.countries.set(countries);
                
                if(this.priorities()) {
                    const defaultPriority = this.priorities().find((priority) => priority.symbol == DEFAULT_PRIORITY_SYMBOL);
                    if(defaultPriority?.id) {
                        this.form.get('priorityId')?.setValue(defaultPriority.id);
                    }
                }

                if(this.statuses()) {
                    const defaultStatus = this.statuses().find((status) => status.symbol == DEFAULT_STATUS_SYMBOL);
                    if(defaultStatus?.id) {
                        this.form.get('statusId')?.setValue(defaultStatus?.id);
                    }
                }

                if(this.countries()) {
                    const defaultCountry = this.countries().find((country) => country.symbol == DEFAULT_COUNTRY_SYMBOL);
                    if(defaultCountry?.id) {
                        this.form.get('countryId')?.setValue(defaultCountry?.id);
                        this.loadProvinces(defaultCountry?.id);
                    }
                }
            }),
            takeUntilDestroyed(this.destroyRef),
        )
        .subscribe({
            error: (err) => {
                console.error(err);
            }
        });
    }

    protected loadProvinces(countryId?: number): void {
        this.provinceService.index(countryId ? { countryId: countryId } : undefined).subscribe({
            next: (res) => {
                this.provinces.set(res.data?.items ?? []);
            },
        });
    }

    protected loadCities(provinceId?: number): void {
        this.cityService.index(provinceId ? { provinceId: provinceId } : undefined).subscribe({
            next: (res) => {
                this.cities.set(res.data?.items ?? []);
            },
        });
    }

    protected saveOrder(): void {
        if(this.form.invalid) {
            this.form.markAllAsDirty();
            this.toastService.show(this.translateService.instant('form.error'), ToastType.danger);
            return;
        }

        // TODO: send the data
    }

    protected onCountryChange(event?: any): void {
        const countryId = event?.id

        if(!countryId || countryId == null || countryId == undefined) {
            this.provinces.set([]);
            this.cities.set([]);
            this.form.get('provinceId')?.reset();
            this.form.get('cityId')?.reset();
            return;
        }

        this.loadProvinces(countryId);
    }

    protected onProvinceChange(event?: any): void {
        const provinceId = event?.id

        if(!provinceId || provinceId == null || provinceId == undefined) {
            this.cities.set([]);
            this.form.get('cityId')?.reset();
            return;
        }

        this.loadCities(provinceId);
    }

    ngOnDestroy(): void {
        this.modal?.dispose();
    }
}
