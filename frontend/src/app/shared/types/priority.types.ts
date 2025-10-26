import { PriorityType } from "../enums/enums";

export interface PriorityItem {
    id: number;
    type: PriorityType;
    label: string;
}