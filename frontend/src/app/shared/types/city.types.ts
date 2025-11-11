import { BaseFilterParams } from "./rest.types";

export interface CityFilterParams extends BaseFilterParams {}

export interface CityItem {
    id: number;
    name: string;
}