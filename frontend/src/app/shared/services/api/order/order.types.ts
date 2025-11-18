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