import { BaseFilterParams } from "./rest.types";

export interface CountryFilterParams extends BaseFilterParams {}

export interface CountryItem {
    id: number;
    symbol: string;
    name: string;
}