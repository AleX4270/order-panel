import { Priority } from "../enums/priority.enum";
import { BaseFilterParams } from "./rest.types";

export interface PriorityFilterParams extends BaseFilterParams {
    term?: string;
}

export interface PriorityItem {
    id: number;
    symbol: Priority;
    isActive: boolean;
    name: string;
}