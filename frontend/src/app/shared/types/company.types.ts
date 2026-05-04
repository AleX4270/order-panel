import { Coordinates } from "./address.types";

export interface CompanyItem {
    id: number;
    name: string;
    address: string;
    postalCode?: string;
    cityId: number;
    provinceId: number;
    countryId: number;
    coordinates: Coordinates;
}

export interface CompanyParams {
    id?: number;
    name: string;
    countryId: number;
    provinceId: number;
    cityId: number;
    cityName?: string;
    postalCode?: string;
    address: string;
}