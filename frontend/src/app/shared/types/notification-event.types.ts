import { BaseFilterParams } from "./rest.types";

export interface NotificationEventFilterParams extends BaseFilterParams {}

export interface NotificationEventItem {
    id: number;
    symbol: string;
    name: string;
}
