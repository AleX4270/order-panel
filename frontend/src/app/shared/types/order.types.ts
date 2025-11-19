import { BaseFilterParams } from "./rest.types";

export interface OrderFilterParams extends BaseFilterParams {}

export interface OrderItem {
    id: number;
    address: string;
    postalCode: string;
    cityName: string;
    provinceName: string;
    prioritySymbol: string;
    priorityName: string;
    statusSymbol: string;
    dateCreated: Date;
    dateDeadline: Date;
    remarks: string;
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