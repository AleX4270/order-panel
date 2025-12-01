import { LanguageType } from "../enums/enums";

export interface User {
    username: string;
    language?: LanguageType;
    isAuthenticated: boolean;
}

export interface UserItem {}
