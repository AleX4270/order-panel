import { FilterType } from "../enums/filter-type.enum";

export interface IStrategyFactory<T> {
    create(type: FilterType): T;
}