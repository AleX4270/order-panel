import { Injectable } from "@angular/core";
import { IFiltersStrategy } from "../../interfaces/filters/filters-strategy.interface";
import { FilterModel } from "../../types/filters.types";

@Injectable({ providedIn: 'root' })
export class UserListFiltersStrategy implements IFiltersStrategy {
    private filters: FilterModel[] = [];

    private initSchema(): void {
        this.filters = [
            {
                key: 'allFields',
                label: 'userListFilters.allFields',
                type: 'text',
                placeholder: 'userListFilters.allFieldsPlaceholder',
            },
            {
                key: 'dateCreated',
                label: 'userListFilters.dateCreated',
                type: 'date',
            },
            {
                key: 'dateUpdated',
                label: 'userListFilters.dateUpdated',
                type: 'date',
            },
        ];
    }

    getFilters(): FilterModel[] {
        this.initSchema();
        return this.filters;
    }
}