import { inject, Injectable, Type } from "@angular/core";
import { FilterType } from "../enums/filter-type.enum";
import { IFiltersStrategy } from "../interfaces/filters/filters-strategy.interface";
import { IStrategyFactory } from "../interfaces/strategy-factory.interface";
import { OrderListFiltersStrategy } from "../strategies/filters/order-list-filters.strategy";

@Injectable({ providedIn: 'root' })
export class FiltersStrategyFactory implements IStrategyFactory<IFiltersStrategy | null> {
    create(type: FilterType): IFiltersStrategy | null {
        switch(type) {
            case FilterType.orderListFilters:
                return inject(OrderListFiltersStrategy);
            default:
                return null;
        }
    }
}