import { Injectable } from '@angular/core';
import { PaginationItem } from '../../types/pagination.types';
import { FilterType } from '../../enums/filter-type.enum';

interface FilterStore {
    filters: Partial<Record<string, string | number[] | null>> | null;
    pagination: PaginationItem | null;
}

@Injectable({
    providedIn: 'root'
})
export class FilterService {
    private filtersStore: Partial<Record<FilterType, FilterStore>> = {};

    public setFilters(type: FilterType, filterValues: Partial<Record<string, string | number[] | null>>): void {
        this.filtersStore[type] = {
            filters: filterValues,
            pagination: this.filtersStore[type]?.pagination ?? null,
        }
    }

    public setPagination(type: FilterType, paginationValues: PaginationItem): void {
        this.filtersStore[type] = {
            filters: this.filtersStore[type]?.filters ?? null,
            pagination: paginationValues,
        }
    }

    public getFilters(type: FilterType): FilterStore | null {
        const filters = this.filtersStore[type];

        if(filters) {
            return filters;
        }

        return null;
    }
}
