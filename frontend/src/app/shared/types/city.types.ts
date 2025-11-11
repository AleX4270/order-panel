import { BaseFilterParams } from "./rest.types";

export interface CityFilterParams extends BaseFilterParams {
    provinceId?: number;
}

export interface CityItem {
    id: number;
    name: string;
}