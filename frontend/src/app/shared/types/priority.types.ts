import { PriorityType } from "../enums/enums";
import { BaseFilterParams } from "./rest.types";

export interface PriorityFilterParams extends BaseFilterParams {}

export interface PriorityItem {
    id: number;
    type: PriorityType;
    label: string;
}