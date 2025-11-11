import { BaseFilterParams } from "./rest.types";

export interface ProvinceFilterParams extends BaseFilterParams {
    countryId?: number;
}

export interface ProvinceItem {
    id: number;
    name: string;
}