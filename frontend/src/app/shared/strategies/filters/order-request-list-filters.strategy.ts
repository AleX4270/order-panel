import { Injectable } from "@angular/core";
import { IFiltersStrategy } from "../../interfaces/filters/filters-strategy.interface";
import { FilterModel } from "../../types/filters.types";

@Injectable({ providedIn: 'root' })
export class OrderRequestListFiltersStrategy implements IFiltersStrategy {
    private filters: FilterModel[] = [];

    private initSchema(): void {
        this.filters = [
            {
                key: 'allFields',
                label: 'orderRequestListFilters.allFields',
                type: 'text',
                placeholder: 'orderRequestListFilters.allFieldsPlaceholder',
            },
        ];
    }

    getFilters(): FilterModel[] {
        this.initSchema();
        return this.filters;
    }
}