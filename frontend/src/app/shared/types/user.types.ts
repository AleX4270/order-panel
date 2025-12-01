import { LanguageType } from "../enums/enums";
import { BaseFilterParams } from "./rest.types";

export interface User {
    username: string;
    language?: LanguageType;
    isAuthenticated: boolean;
}

export interface UserFilterParams extends BaseFilterParams {
    allFields?: string;
    dateCreated?: string;
    dateUpdated?: string;
}

export interface UserItem {
    id: number;
    name: string;
    firstName: string;
    lastName: string;
    email: string;
    dateCreated: string;
    dateUpdated: string;
}
