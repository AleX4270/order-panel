import { Priority } from "../enums/priority.enum";
import { BaseFilterParams } from "./rest.types";

export interface PriorityFilterParams extends BaseFilterParams {}

export interface PriorityItem {
    id: number;
    symbol: Priority;
    isActive: boolean;
    name: string;
}