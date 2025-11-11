import { BaseFilterParams } from "./rest.types";

export interface ProvinceFilterParams extends BaseFilterParams {}

export interface ProvinceItem {
    id: number;
    name: string;
}