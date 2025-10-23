import { inject } from "@angular/core";
import { IFiltersStrategy } from "../../interfaces/filters/filters-strategy.interface";
import { FilterModel } from "../../types/filters.types";
import { TranslateService } from "@ngx-translate/core";

export class OrderListFiltersStrategy implements IFiltersStrategy {
    private readonly translate = inject(TranslateService);
    private filters: FilterModel[] = [];

    private initSchema(): void {
        this.filters = [
            {
                key: 'allFields',
                label: this.translate.instant('orderListFilters.allFields'),
                type: 'text',
                placeholder: this.translate.instant('orderListFilters.allFields'),
            },
        ];
    }

    getFilters(): FilterModel[] {
        this.initSchema();
        return this.filters;
    }
}