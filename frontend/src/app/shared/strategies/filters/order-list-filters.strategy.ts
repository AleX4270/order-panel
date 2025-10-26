import { Injectable } from "@angular/core";
import { IFiltersStrategy } from "../../interfaces/filters/filters-strategy.interface";
import { FilterModel } from "../../types/filters.types";

@Injectable({ providedIn: 'root' })
export class OrderListFiltersStrategy implements IFiltersStrategy {
    private filters: FilterModel[] = [];

    private initSchema(): void {
        this.filters = [
            {
                key: 'allFields',
                label: 'orderListFilters.allFields',
                type: 'text',
                placeholder: 'orderListFilters.allFieldsPlaceholder',
            },
            {
                key: 'priority',
                label: 'orderListFilters.priority',
                type: 'multi-select',
                placeholder: 'orderListFilters.priorityPlaceholder',
            },
            {
                key: 'status',
                label: 'orderListFilters.status',
                type: 'multi-select',
                placeholder: 'orderListFilters.statusPlaceholder',
            },
            {
                key: 'dateCreation',
                label: 'orderListFilters.dateCreation',
                type: 'date',
            },
            {
                key: 'dateDeadline',
                label: 'orderListFilters.dateDeadline',
                type: 'date',
            },
        ];
    }

    getFilters(): FilterModel[] {
        this.initSchema();
        return this.filters;
    }
}