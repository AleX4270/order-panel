import { DatePipe } from '@angular/common';
import { Component, computed, DestroyRef, ElementRef, inject, output, Signal, signal, ViewChild, WritableSignal } from '@angular/core';
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
import { distinctUntilChanged, forkJoin, map, tap } from 'rxjs';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { DEFAULT_COUNTRY_SYMBOL, DEFAULT_PRIORITY_SYMBOL, DEFAULT_STATUS_SYMBOL } from '../../app.constants';
import { ProvinceService } from '../../shared/services/api/province/province.service';
import { CityService } from '../../shared/services/api/city/city.service';
import { ToastService } from '../../shared/services/toast/toast.service';
import { ToastType } from '../../shared/enums/enums';
import { OrderService } from '../../shared/services/api/order/order.service';
import { OrderItem, OrderParams } from '../../shared/types/order.types';

@Component({
    selector: 'app-order-form-modal',
    imports: [ReactiveFormsModule, NgSelectComponent, InputErrorLabelComponent, TranslatePipe],
    providers: [DatePipe],
    template: `
        <dialog #modalRef class="modal">
            <div class="modal-box max-w-3xl">
                <div class="header">
                    <h5 class="text-primary font-semibold text-xl">{{ ("orderForm." + (isEditScenario() ? "updateTitle" : "createTitle") | translate) + (isEditScenario() ? ' - #' + this.orderId() : '') }}</h5>
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                </div>

                <div class="content [&_label]:text-xs [&_label]:font-light [&_label]:mb-1">
                    @if(!isLoading() && form) {
                        <form [formGroup]="form" class="w-full">
                            @if(isEditScenario()) {
                                <div class="w-full flex flex-col mt-3">
                                    <label for="orderNumber" class="label">{{ "orderForm.orderNumber" | translate }}</label>
                                    <input
                                        type="text"
                                        formControlName="orderNumber"
                                        id="orderNumber"
                                        name="orderNumber"
                                        class="input text-xs w-full"
                                        [placeholder]="'orderForm.orderNumberPlaceholder' | translate"
                                    />
                                </div>
                                <app-input-error-label [control]="form.get('orderNumber')" />

                                <div class="divider"></div>
                            }
                            
                            <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap mt-4">
                                <div class="flex flex-col w-full md:w-1/4">
                                    <label for="countryId" class="label">{{ "orderForm.country" | translate }}</label>
                                    <ng-select
                                        formControlName="countryId"
                                        [items]="countries()"
                                        bindValue="id"
                                        bindLabel="name"
                                        [multiple]="false"
                                        [placeholder]="'orderForm.countryPlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('countryId')" />
                                </div>

                                <div class="flex flex-col w-full md:w-1/3">
                                    <label for="provinceId" class="label">{{ "orderForm.province" | translate }}</label>
                                    <ng-select
                                        formControlName="provinceId"
                                        [items]="provinces()"
                                        bindValue="id"
                                        bindLabel="name"
                                        [multiple]="false"
                                        [placeholder]="'orderForm.provincePlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('provinceId')" />
                                </div>

                                <div class="flex flex-col w-full md:w-1/3">
                                    <label for="cityId" class="label">{{ "orderForm.city" | translate }}</label>
                                    <ng-select 
                                        formControlName="cityId"
                                        [items]="cities()"
                                        bindValue="id"
                                        bindLabel="name"
                                        [multiple]="false"
                                        [placeholder]="'orderForm.cityPlaceholder' | translate"
                                        [addTagText]="'orderForm.addCity' | translate"
                                        [addTag]="addNewCity"
                                        (change)="onCityChange($event)"
                                    />
                                    <app-input-error-label [control]="form.get('cityId')" />
                                </div>

                                <div class="flex flex-col w-full md:w-1/3">
                                    <label for="postalCode" class="label">{{ "orderForm.postalCode" | translate }}</label>
                                    <input
                                        type="text"
                                        formControlName="postalCode"
                                        id="postalCode"
                                        name="postalCode"
                                        class="input text-xs w-full"
                                        [placeholder]="'orderForm.postalCodePlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('postalCode')" />
                                </div>

                                <div class="flex flex-col w-full md:flex-1">
                                    <label for="address" class="label">{{ "orderForm.address" | translate }}</label>
                                    <input
                                        type="text"
                                        formControlName="address"
                                        id="address"
                                        name="address"
                                        class="input text-xs w-full"
                                        [placeholder]="'orderForm.addressPlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('address')" />
                                </div>
                            </div>

                            <div class="divider md:hidden"></div>

                            <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap md:mt-4">
                                <div class="flex flex-col w-full md:w-1/4">
                                    <label for="priorityId" class="label">{{ "orderForm.priority" | translate }}</label>
                                    <ng-select 
                                        formControlName="priorityId"
                                        [items]="priorities()"
                                        bindValue="id"
                                        bindLabel="name"
                                        [multiple]="false"
                                        [placeholder]="'orderForm.priorityPlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('priorityId')" />
                                </div>

                                <div class="flex flex-col w-full md:w-1/4">
                                    <label for="statusId" class="label">{{ "orderForm.status" | translate }}</label>
                                    <ng-select 
                                        formControlName="statusId"
                                        [items]="statuses()"
                                        bindValue="id"
                                        bindLabel="name"
                                        [multiple]="false"
                                        [placeholder]="'orderForm.statusPlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('statusId')" />
                                </div>

                                <div class="flex flex-col w-full md:flex-1">
                                    <label for="phoneNumber" class="label">{{ "orderForm.phoneNumber" | translate }}</label>
                                    <input
                                        type="tel"
                                        formControlName="phoneNumber"
                                        id="phoneNumber"
                                        name="phoneNumber"
                                        class="input text-xs w-full"
                                        [placeholder]="'orderForm.phoneNumberPlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('phoneNumber')" />
                                </div>
                            </div>

                            <div class="divider md:hidden"></div>

                            <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap md:mt-4">
                                <div class="flex flex-col w-full md:flex-1">
                                    <label for="dateCreation" class="label">{{ "orderForm.dateCreation" | translate }}</label>
                                    <input
                                        type="date"
                                        formControlName="dateCreation"
                                        id="dateCreation"
                                        name="dateCreation"
                                        class="input text-xs w-full"
                                        [min]="currentDate()"
                                    />
                                    <app-input-error-label [control]="form.get('dateCreation')" />
                                </div>

                                <div class="flex flex-col w-full md:flex-1">
                                    <label for="dateDeadline" class="label">{{ "orderForm.dateDeadline" | translate }}</label>
                                    <input
                                        type="date"
                                        formControlName="dateDeadline"
                                        id="dateDeadline"
                                        name="dateDeadline"
                                        class="input text-xs w-full"
                                        [min]="form.get('dateCreation')?.value"
                                    />
                                    <app-input-error-label [control]="form.get('dateDeadline')" />
                                </div>

                                @if(isEditScenario()) {
                                    <div class="flex flex-col w-full md:flex-1">
                                        <label for="dateCompleted" class="label">{{ "orderForm.dateCompleted" | translate }}</label>
                                        <input
                                            type="date"
                                            formControlName="dateCompleted"
                                            id="dateCompleted"
                                            name="dateCompleted"
                                            class="input text-xs w-full"
                                            [min]="form.get('dateCreation')?.value"
                                        />
                                        <app-input-error-label [control]="form.get('dateCompleted')" />
                                    </div>
                                }
                            </div>

                            <div class="divider md:hidden"></div>

                            <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap md:mt-4">
                                <div class="flex flex-col w-full">
                                    <label for="remarks" class="label">{{ "orderForm.remarks" | translate }}</label>
                                    <textarea
                                        id="remarks"
                                        name="remarks"
                                        rows="4"
                                        formControlName="remarks"
                                        class="textarea mt-2 text-xs w-full"
                                        [placeholder]="'orderForm.remarksPlaceholder' | translate"
                                    ></textarea>
                                    <app-input-error-label [control]="form.get('remarks')" />
                                </div>
                            </div>
                        </form>
                    }
                </div>

                <div class="footer">
                    <div class="modal-action w-full">
                        <form method="dialog" class="flex items-center gap-3">
                            <button class="btn btn-outline btn-sm btn-error">{{"basic.cancel" | translate}}</button>
                            <button type="button" class="btn btn-primary btn-sm" (click)="saveOrder()">{{"basic.save" | translate}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </dialog>
    `,
    styles: [``],
})
export class OrderFormModalComponent {
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
    private readonly orderService: OrderService = inject(OrderService);

    protected form!: FormGroup;
    protected orderId: WritableSignal<number | null> = signal<number | null>(null);
    protected isEditScenario: WritableSignal<boolean> = signal<boolean>(false);
    protected isLoading: WritableSignal<boolean> = signal<boolean>(false);

    protected priorities: WritableSignal<PriorityItem[]> = signal<PriorityItem[]>([]);
    protected statuses: WritableSignal<StatusItem[]> = signal<StatusItem[]>([]);
    protected countries: WritableSignal<CountryItem[]> = signal<CountryItem[]>([]);
    protected provinces: WritableSignal<ProvinceItem[]> = signal<ProvinceItem[]>([]);
    protected cities: WritableSignal<CityItem[]> = signal<CityItem[]>([]);

    protected orderSaved = output<void>();

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

    public showForm(orderId?: number): void {
        this.isLoading.set(true);

        if(orderId) {
            this.orderId.set(orderId);
            this.isEditScenario.set(true);
            this.loadDetails(orderId);
        }

        this.initForm();
        this.loadFormOptionsData();
        this.openModal();        
    }
    
    protected openModal(): void {
        const modal = this.orderFormModal.nativeElement as HTMLDialogElement;
        if(!modal) {
            return;
        }

        modal.showModal();
        this.isLoading.set(false);
    }

    protected closeModal(): void {
        const modal = this.orderFormModal.nativeElement as HTMLDialogElement;
        if(!modal) {
            return;
        }

        modal.close();

        this.isEditScenario.set(false);
        this.orderId.set(null);
        this.form.reset();
    }

    private initForm(): void {
        this.form = this.formBuilder.group({
            id: [null],
            orderNumber: [{ value: null, disabled: true }],
            countryId: [null, Validators.required],
            provinceId: [null, Validators.required],
            cityId: [null, Validators.required],
            cityName: [null],
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

        this.registerFormChanges();
    }

    private registerFormChanges(): void {
        const countryField = this.form.get('countryId');
        const provinceField = this.form.get('provinceId');

        if(!countryField || !provinceField) {
            return;
        }

        countryField.valueChanges.pipe(
            distinctUntilChanged(),
            takeUntilDestroyed(this.destroyRef),
        )
        .subscribe({
            next: (countryId: number | null) => {
                this.provinces.set([]);
                this.cities.set([]);
                provinceField.reset();
                this.form.get('cityId')?.reset();

                if(countryId) {
                    this.loadProvinces(countryId);
                }
            }
        });

        provinceField.valueChanges.pipe(
            distinctUntilChanged(),
            takeUntilDestroyed(this.destroyRef),
        )
        .subscribe({
            next: (provinceId: number | null) => {
                this.cities.set([]);
                this.form.get('cityId')?.reset();

                if(provinceId) {
                    this.loadCities(provinceId);
                }
            }
        });
    }

    private loadDetails(orderId: number): void {
        this.orderService.show(orderId).subscribe({
            next: (res) => {
                const order: OrderItem | null = res.data;
                
                if(!order) {
                    return;
                }

                this.form.patchValue({
                    id: order.id,
                    orderNumber: order.id,
                    countryId: order.countryId,
                    provinceId: order.provinceId,
                    cityId: order.cityId,
                    postalCode: order.postalCode,
                    address: order.address,
                    phoneNumber: order.phoneNumber,
                    priorityId: order.priorityId,
                    statusId: order.statusId,
                    dateCreation: order.dateCreated,
                    dateDeadline: order.dateDeadline,
                    dateCompleted: order.dateCompleted ?? null,
                    remarks: order.remarks
                });
            },
            error: (err) => {
                console.error(err);
            }
        });
    }

    private loadFormOptionsData(): void {
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
                    if(!this.isEditScenario() && defaultPriority?.id) {
                        this.form.get('priorityId')?.setValue(defaultPriority.id);
                    }
                }

                if(this.statuses()) {
                    const defaultStatus = this.statuses().find((status) => status.symbol == DEFAULT_STATUS_SYMBOL);
                    if(!this.isEditScenario() && defaultStatus?.id) {
                        this.form.get('statusId')?.setValue(defaultStatus?.id);
                    }
                }

                if(this.countries()) {
                    const defaultCountry = this.countries().find((country) => country.symbol == DEFAULT_COUNTRY_SYMBOL);
                    if(!this.isEditScenario() && defaultCountry?.id) {
                        this.form.get('countryId')?.setValue(defaultCountry?.id);
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

        const formValues = this.form.value;

        let orderParams = {
            countryId: formValues.countryId,
            provinceId: formValues.provinceId,
            cityId: formValues.cityId,
            address: formValues.address,
            phoneNumber: formValues.phoneNumber,
            priorityId: formValues.priorityId,
            statusId: formValues.statusId,
            dateCreation: formValues.dateCreation,
            dateDeadline: formValues.dateDeadline,
        } as OrderParams;

        if(formValues.id) {
            orderParams.id = formValues.id;
        }

        if(formValues.cityName) {
            orderParams.cityName = formValues.cityName;
        }

        if(formValues.postalCode) {
            orderParams.postalCode = formValues.postalCode;
        }

        if(formValues.dateCompleted) {
            orderParams.dateCompleted = formValues.dateCompleted;
        }

        if(formValues.remarks) {
            orderParams.remarks = formValues.remarks;
        }

        const method = this.isEditScenario()
            ? this.orderService.update(orderParams)
            : this.orderService.store(orderParams);

        method.subscribe({
            next: (res) => {
                this.toastService.show(
                    this.translateService.instant('orderForm.saveSuccessMessage'),
                    ToastType.success,
                );
                this.orderSaved.emit();
            },
            error: (err) => {
                console.error(err);
                this.toastService.show(
                    this.translateService.instant('orderForm.saveErrorMessage'),
                    ToastType.danger,
                );
            },
            complete: () => {
                this.closeModal();
            }
        });
    }

    protected onCityChange(event: any): void {
        if(event && !event.id && event.name) {
            this.form.get('cityName')?.setValue(event.name);
            return;
        }

        this.form.get('cityName')?.reset();
    }
}
