import { BaseFilterParams } from "./rest.types";

export interface StatusFilterParams extends BaseFilterParams {}

export interface StatusItem {
    id: number;
    symbol: string;
    name: string;
}