import { BaseFilterParams } from "./rest.types";

export interface RoleFilterParams extends BaseFilterParams {};

export interface RoleItem {
    id: number;
    symbol: string;
    name: string;
}