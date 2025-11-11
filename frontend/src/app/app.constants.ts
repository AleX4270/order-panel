import { Country } from "./shared/enums/country.enum";
import { Priority } from "./shared/enums/priority.enum";
import { Status } from "./shared/enums/status.enum";

export const TOAST_DURATION: number = 3000;
export const MAX_TOAST_STACK_ELEMENTS: number = 5;
export const PAGINATION_START_PAGE: number = 1;
export const PAGINATION_PAGE_SIZE: number = 10;

export const DEFAULT_COUNTRY_SYMBOL: Country = Country.pl;
export const DEFAULT_PRIORITY_SYMBOL: Priority = Priority.standard;
export const DEFAULT_STATUS_SYMBOL: Status = Status.in_progress;