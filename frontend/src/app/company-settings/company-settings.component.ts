import { Component, ElementRef, inject, OnInit, signal, viewChild } from '@angular/core';
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
                    <form [formGroup]="form" class="w-full">
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
                        <app-address-subform #addressForm [form]="form" [setDefaultCountry]="false" />

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
                            <app-button type="button" (click)="getAddressCoordinates()" classList="btn btn-soft btn-neutral">{{"basic.find" | translate}}</app-button>
                            <app-button type="submit" classList="btn btn-primary">{{"basic.saveChanges" | translate}}</app-button>
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

    protected form!: FormGroup;

    protected addressFormComponent = viewChild<AddressSubformComponent>('addressForm');

    protected companyCoordinates = signal<Coordinates>({longitude: 16.9252, latitude: 52.4064});

    ngOnInit(): void {
        this.initForm();
    }

    private initForm(): void {
        this.form = this.formBuilder.group({
            id: [null, Validators.required],
            name: [null, Validators.required],
            countryId: [null, Validators.required],
            provinceId: [null, Validators.required],
            cityId: [null, Validators.required],
            cityName: [null],
            postalCode: [null, Validators.maxLength(32)],
            address: [null, [Validators.required, Validators.maxLength(255)]],
        });
    }

    protected getAddressCoordinates(): void {
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
                console.log(res);
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
}
