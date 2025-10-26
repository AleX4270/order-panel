import { FilterModel } from "../../types/filters.types";

export interface IFiltersStrategy {
    getFilters(): FilterModel[];
}