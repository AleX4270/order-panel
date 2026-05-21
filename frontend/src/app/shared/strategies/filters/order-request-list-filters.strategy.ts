import { inject, Injectable } from "@angular/core";
import { IFiltersStrategy } from "../../interfaces/filters/filters-strategy.interface";
import { FilterModel, FilterOption } from "../../types/filters.types";
import { ValidationError } from "../../errors/validation.error";
import { TranslateService } from "@ngx-translate/core";

@Injectable({ providedIn: 'root' })
export class OrderRequestListFiltersStrategy implements IFiltersStrategy {
    private filters: FilterModel[] = [];
    private translateService = inject(TranslateService);

    private initSchema(): void {
        this.filters = [
            {
                key: 'allFields',
                label: 'orderRequestListFilters.allFields',
                type: 'text',
                placeholder: 'orderRequestListFilters.allFieldsPlaceholder',
            },
            {
                key: 'distanceFromHeadquarters',
                label: 'orderRequestListFilters.distanceFromHeadquarters',
                type: 'number',
                placeholder: 'orderRequestListFilters.distanceFromHeadquarters',
                validate: (value: string | FilterOption[] | null) => {
                    if (!value) {
                        return true;
                    }

                    const distance = Number(value);

                    if (isNaN(distance)) {
                        throw new ValidationError(this.translateService.instant('orderListFilters.distanceNotANumberError'));
                    }

                    if (distance <= 0) {
                        throw new ValidationError(this.translateService.instant('orderListFilters.distanceZeroError'));
                    }

                    return true;
                }
            },
            {
                key: 'dateCreation',
                label: 'orderRequestListFilters.dateCreation',
                type: 'date',
            },
        ];
    }

    getFilters(): FilterModel[] {
        this.initSchema();
        return this.filters;
    }
}