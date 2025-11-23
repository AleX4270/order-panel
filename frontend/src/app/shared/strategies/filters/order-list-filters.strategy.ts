import { inject, Injectable } from "@angular/core";
import { IFiltersStrategy } from "../../interfaces/filters/filters-strategy.interface";
import { FilterModel, FilterOption } from "../../types/filters.types";
import { PriorityService } from "../../services/api/priority/priority.service";
import { map } from "rxjs";
import { StatusService } from "../../services/api/status/status.service";

@Injectable({ providedIn: 'root' })
export class OrderListFiltersStrategy implements IFiltersStrategy {
    private filters: FilterModel[] = [];
    private readonly priorityService = inject(PriorityService);
    private readonly statusService = inject(StatusService);

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
                loader: (term?: string) => {
                    return this.priorityService.index({term: term}).pipe(
                        map((res) => {
                            const items = res.data?.items ?? [];
                            return items.map(item => ({
                                id: item.id,
                                name: item.name,
                            }) as FilterOption)
                        })
                    );
                },
            },
            {
                key: 'status',
                label: 'orderListFilters.status',
                type: 'multi-select',
                placeholder: 'orderListFilters.statusPlaceholder',
                loader: (term?: string) => {
                    return this.statusService.index({term: term}).pipe(
                        map((res) => {
                            const items = res.data?.items ?? [];
                            return items.map(item => ({
                                id: item.id,
                                name: item.name,
                            }) as FilterOption)
                        })
                    );
                }
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