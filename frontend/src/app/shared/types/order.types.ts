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
    cityId: number;
    cityName: string;
    provinceId: number;
    provinceName: string;
    countryId: number;
    priorityId: number;
    prioritySymbol: Priority;
    priorityName: string;
    statusId: number;
    statusName: string;
    statusSymbol: Status;
    dateCreated: string;
    dateDeadline: string;
    dateCompleted: string;
    phoneNumber: string;
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