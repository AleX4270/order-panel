import { Coordinates } from "./address.types";
import { BaseFilterParams } from "./rest.types";

export interface OrderRequestFilterParams extends BaseFilterParams {
    distanceFromHeadquarters?: number;
    dateCreation?: string;
    allFields?: string;
}

export interface OrderRequestItem {
    id: number;
    firstName: string;
    lastName: string;
    email: string;
    phoneNumber: string;
    address: string;
    postalCode: string;
    cityName: string;
    provinceName: string;
    remarks: string | null;
    dateCreated: string;
    coordinates: Coordinates;
    distance: number;
}
