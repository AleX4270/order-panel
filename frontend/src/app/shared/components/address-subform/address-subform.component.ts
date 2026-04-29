import { Component, DestroyRef, inject, input, OnInit, signal, WritableSignal } from '@angular/core';
import { NgSelectComponent } from '@ng-select/ng-select';
import { TranslatePipe } from '@ngx-translate/core';
import { InputErrorLabelComponent } from '../input-error-label/input-error-label.component';
import { FormGroup, ReactiveFormsModule } from '@angular/forms';
import { CountryItem } from '../../types/country.types';
import { ProvinceItem } from '../../types/province.types';
import { CityItem } from '../../types/city.types';
import { distinctUntilChanged } from 'rxjs';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { ProvinceService } from '../../services/api/province/province.service';
import { CityService } from '../../services/api/city/city.service';
import { CountryService } from '../../services/api/country/country.service';
import { DEFAULT_COUNTRY_SYMBOL } from '../../../app.constants';

@Component({
    selector: 'app-address-subform',
    imports: [
        NgSelectComponent,
        TranslatePipe,
        InputErrorLabelComponent,
        ReactiveFormsModule,
    ],
    template: `
        <div [formGroup]="form()" class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap mt-4">
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
                <app-input-error-label [control]="form().get('countryId')" />
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
                <app-input-error-label [control]="form().get('provinceId')" />
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
                <app-input-error-label [control]="form().get('cityId')" />
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
                <app-input-error-label [control]="form().get('postalCode')" />
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
                <app-input-error-label [control]="form().get('address')" />
            </div>
        </div>
    `,
    styles: [``],
})
export class AddressSubformComponent implements OnInit {
    private readonly destroyRef: DestroyRef = inject(DestroyRef);
    private readonly countryService: CountryService = inject(CountryService);
    private readonly provinceService: ProvinceService = inject(ProvinceService);
    private readonly cityService: CityService = inject(CityService);

    public form = input.required<FormGroup>();
    public setDefaultCountry = input.required<boolean>();

    protected countries: WritableSignal<CountryItem[]> = signal<CountryItem[]>([]);
    protected provinces: WritableSignal<ProvinceItem[]> = signal<ProvinceItem[]>([]);
    protected cities: WritableSignal<CityItem[]> = signal<CityItem[]>([]);

    ngOnInit(): void {
        this.registerFormChanges();
        this.loadCountries();
    }

    private registerFormChanges(): void {
        const countryField = this.form().get('countryId');
        const provinceField = this.form().get('provinceId');

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
                this.form().get('cityId')?.reset();

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
                this.form().get('cityId')?.reset();

                if(provinceId) {
                    this.loadCities(provinceId);
                }
            }
        });
    }

    protected loadCountries(): void {
        this.countryService.index().subscribe({
            next: (res) => {
                this.countries.set(res.data?.items ?? []);

                if(this.countries()) {
                    const defaultCountry = this.countries().find((country) => country.symbol == DEFAULT_COUNTRY_SYMBOL);
                    if(this.setDefaultCountry() && defaultCountry?.id) {
                        this.form().get('countryId')?.setValue(defaultCountry?.id);
                    }
                }
            },
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

    protected onCityChange(event: any): void {
        if(event && !event.id && event.name) {
            this.form().get('cityName')?.setValue(event.name);
            return;
        }

        this.form().get('cityName')?.reset();
    }
}
