import { Component, inject, OnInit, signal, viewChild } from '@angular/core';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { CardComponent } from '../shared/components/card/card.component';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { InputErrorLabelComponent } from '../shared/components/input-error-label/input-error-label.component';
import { AddressSubformComponent } from '../shared/components/address-subform/address-subform.component';
import { AlertComponent } from '../shared/components/alert/alert.component';
import { ButtonComponent } from '../shared/components/button/button.component';
import { CompanyHeadquartersMapComponent } from "../shared/components/company-headquarters-map/company-headquarters-map.component";
import { NgIconComponent, provideIcons } from "@ng-icons/core";
import { faSolidCircleInfo } from '@ng-icons/font-awesome/solid';
import { NominatimService } from '../shared/services/nominatim/nominatim.service';
import { Coordinates } from '../shared/types/address.types';
import { ToastService } from '../shared/services/toast/toast.service';
import { ToastType } from '../shared/enums/enums';
import { CompanyService } from '../shared/services/api/company/company.service';
import { CompanyItem, CompanyParams } from '../shared/types/company.types';
import { finalize, skip, throttleTime } from 'rxjs';
import { toObservable } from '@angular/core/rxjs-interop';
import { DEFAULT_COORDINATES } from '../shared/constants/map.const';

@Component({
    selector: 'app-company-settings',
    imports: [
    TranslatePipe,
    CardComponent,
    ReactiveFormsModule,
    InputErrorLabelComponent,
    AddressSubformComponent,
    AlertComponent,
    ButtonComponent,
    CompanyHeadquartersMapComponent,
    NgIconComponent
],
    providers: [provideIcons({faSolidCircleInfo})],
    template: `
        <div class="w-full user-list-header">
            <h1 class="font-semibold text-2xl mb-5">{{'companySettings.header' | translate}}</h1>
        </div>

        <div class="flex flex-col lg:flex-row gap-5 [&_label]:text-xs [&_label]:font-light [&_label]:mb-1">
            <div class="w-full lg:w-2/5">
                <app-card overflowType="visible">
                    <form [formGroup]="form" (ngSubmit)="save()" class="w-full">
                        <h2 class="font-semibold text-sm">{{"companySettings.basicInfo" | translate}}</h2>
                        <div class="divider m-0"></div>
                        <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap mt-1">
                            <div class="flex flex-col w-full">
                                <label for="name" class="label">{{ "companySettings.name" | translate }}</label>
                                <input
                                    type="text"
                                    formControlName="name"
                                    id="name"
                                    name="name"
                                    class="input text-xs w-full"
                                />
                                <app-input-error-label [control]="form.get('name')" />
                                <p class="text-xs font-light text-base-content/40 mt-1">{{"companySettings.nameHint" | translate}}</p>
                            </div>
                        </div>

                        <div class="divider mb-0"></div>

                        <h2 class="font-semibold text-sm">{{"companySettings.headquarters" | translate}}</h2>
                        @if(form) {
                            <app-address-subform #addressForm [form]="form" [setDefaultCountry]="false" />
                        }

                        <div class="mt-4">
                            <app-alert [isSoft]="true">
                                <ng-icon
                                    name="faSolidCircleInfo"
                                    size="19px"
                                ></ng-icon>
                                <span class="text-xs">{{"companySettings.addressHint" | translate}}</span>
                            </app-alert>
                        </div>

                        <div class="mt-4 flex justify-end gap-3">
                            <app-button type="button" (click)="triggerCoordinatesSearch()" classList="btn btn-soft btn-neutral">{{"basic.find" | translate}}</app-button>
                            <app-button type="submit" classList="btn btn-primary" [isDisabled]="!form.dirty">{{"basic.saveChanges" | translate}}</app-button>
                        </div>
                    </form>
                </app-card>
            </div>

            <div class="h-135 w-full lg:w-3/5">
                <app-company-headquarters-map [coordinates]="companyCoordinates()" />
            </div>
        </div>
    `,
    styles: [``],
})
export class CompanySettingsComponent implements OnInit {
    private readonly formBuilder = inject(FormBuilder);
    private readonly nominatimService = inject(NominatimService);
    private readonly toastService = inject(ToastService);
    private readonly translateService = inject(TranslateService);
    private readonly companyService = inject(CompanyService);

    protected form!: FormGroup;

    protected addressFormComponent = viewChild<AddressSubformComponent>('addressForm');

    protected companyCoordinates = signal<Coordinates>(DEFAULT_COORDINATES);
    protected isSubmitted = signal<boolean>(false);
    protected coordinatesSearchTrigger = signal<boolean>(false);

    constructor() {
        toObservable(this.coordinatesSearchTrigger).pipe(
            throttleTime(1000),
            skip(1),
        )
        .subscribe({
            next: () => {
                this.getAddressCoordinates();
            }
        })
    }

    ngOnInit(): void {
        this.initForm();
        this.loadDetails();
    }

    private initForm(): void {
        this.form = this.formBuilder.group({
            id: [null],
            name: [null, Validators.required],
            countryId: [null, Validators.required],
            provinceId: [null, Validators.required],
            cityId: [null, Validators.required],
            cityName: [null],
            postalCode: [null, Validators.maxLength(32)],
            address: [null, [Validators.required, Validators.maxLength(255)]],
        });
    }

    private getAddressCoordinates(): void {
        const address = this.form.get('address')?.value;
        const cityName = this.addressFormComponent()?.cityName;
        const countryName = this.addressFormComponent()?.countryName;
        const postalCode = this.form.get('postalCode')?.value;

        if(!cityName || !countryName || !address) {
            this.toastService.show(this.translateService.instant('companySettings.geocodingInvalidInputError'), ToastType.danger);
            return;
        }

        this.nominatimService.getCoordinates({
            street: address,
            city: cityName,
            country: countryName,
            postalcode: postalCode ?? '',
        }).subscribe({
            next: (res) => {
                const locations = res ?? [];
                const mainLocation = locations[0] ?? null;

                if(locations.length < 1 || !mainLocation) {
                    this.toastService.show(this.translateService.instant('companySettings.geocodingNotFoundError'), ToastType.danger);
                    return;
                }

                this.companyCoordinates.set({
                    longitude: Number(mainLocation.lon),
                    latitude: Number(mainLocation.lat),
                });
            },
        })
    }

    private loadDetails(): void {
        this.companyService.show().subscribe({
            next: (res) => {
                const company: CompanyItem | null = res.data;

                if(!company) {
                    this.toastService.show(this.translateService.instant('companySettings.loadError'), ToastType.danger);
                    return;
                }

                this.companyCoordinates.set(company.coordinates);

                this.form.patchValue({
                    id: company.id,
                    name: company.name,
                    countryId: company.countryId,
                    provinceId: company.provinceId,
                    cityId: company.cityId,
                    postalCode: company.postalCode,
                    address: company.address
                });
            },
            error: (err) => {
                console.error(err);
                this.toastService.show(this.translateService.instant('companySettings.loadError'), ToastType.danger);
            }
        })
    }

    protected save(): void {
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

        const companyParams: CompanyParams = {
            name: formValues.name,
            countryId: formValues.countryId,
            provinceId: formValues.provinceId,
            cityId: formValues.cityId,
            address: formValues.address,
        };

        if(formValues.id) {
            companyParams.id = formValues.id;
        }

        if(formValues.cityName) {
            companyParams.cityName = formValues.cityName;
        }

        if(formValues.postalCode) {
            companyParams.postalCode = formValues.postalCode;
        }

        this.companyService.update(companyParams)
        .pipe(
            finalize(() => {
                this.isSubmitted.set(false);
                this.form.markAsPristine();
            })
        )
        .subscribe({
            next: () => {
                this.toastService.show(this.translateService.instant('companySettings.saveSuccess'), ToastType.success);
            },
            error: (err) => {
                console.error(err);
                this.toastService.show(this.translateService.instant('companySettings.saveError'), ToastType.danger);
            }
        });
    }

    protected triggerCoordinatesSearch(): void {
        this.coordinatesSearchTrigger.set(!this.coordinatesSearchTrigger());
    }
}
