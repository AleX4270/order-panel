import { BaseFilterParams } from "./rest.types";

export interface NotificationChannelFilterParams extends BaseFilterParams {}

export interface NotificationChannelItem {
    id: number;
    symbol: string;
    name: string;
}
