import { PriorityType } from "../enums/enums";
import { BaseFilterParams } from "./rest.types";

export interface PriorityFilterParams extends BaseFilterParams {}

export interface PriorityItem {
    id: number;
    symbol: PriorityType;
    isActive: boolean;
    name: string;
}