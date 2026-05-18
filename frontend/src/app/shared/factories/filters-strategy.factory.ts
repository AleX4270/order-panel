import { inject, Injectable, Injector } from "@angular/core";
import { FilterType } from "../enums/filter-type.enum";
import { IFiltersStrategy } from "../interfaces/filters/filters-strategy.interface";
import { IStrategyFactory } from "../interfaces/strategy-factory.interface";
import { OrderListFiltersStrategy } from "../strategies/filters/order-list-filters.strategy";
import { UserListFiltersStrategy } from "../strategies/filters/user-list-filters.strategy";
import { OrderRequestListFiltersStrategy } from "../strategies/filters/order-request-list-filters.strategy";

@Injectable({ providedIn: 'root' })
export class FiltersStrategyFactory implements IStrategyFactory<IFiltersStrategy | null> {
    private readonly injector = inject(Injector);

    create(type: FilterType): IFiltersStrategy | null {
        switch(type) {
            case FilterType.orderListFilters:
                return this.injector.get(OrderListFiltersStrategy);
            case FilterType.userListFilters:
                return this.injector.get(UserListFiltersStrategy);
            case FilterType.orderRequestListFilters:
                return this.injector.get(OrderRequestListFiltersStrategy);
            default:
                return null;
        }
    }
}