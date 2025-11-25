import { Priority } from "../enums/priority.enum";
import { Status } from "../enums/status.enum";
import { BaseFilterParams } from "./rest.types";

export interface OrderFilterParams extends BaseFilterParams {
    allFields?: string;
    priorityIds?: number[];
    statusIds?: number[];
    dateCreation?: string;
    dateDeadline?: string;
}

export interface OrderItem {
    id: number;
    address: string;
    postalCode: string;
    cityName: string;
    provinceName: string;
    prioritySymbol: Priority;
    priorityName: string;
    statusSymbol: Status;
    dateCreated: Date;
    dateDeadline: Date;
    remarks: string;
    isOverdue: boolean;
}

export interface OrderParams {
    id?: number;
    countryId: number;
    provinceId: number;
    cityId: number;
    cityName?: string;
    postalCode?: string;
    address: string;
    phoneNumber: string;
    priorityId: number;
    statusId: number;
    dateCreation: Date;
    dateDeadline: Date;
    dateCompleted?: Date;
    remarks?: string;
}