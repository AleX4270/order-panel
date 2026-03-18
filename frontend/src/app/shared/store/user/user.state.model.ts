import { LanguageType } from "../../enums/enums";

export interface UserStateModel {
    id: number | null;
    username: string | null;
    language: LanguageType;
    isAuthenticated: boolean;
    permissions: string[];
}

export const initialUserState: UserStateModel = {
    id: null,
    username: null,
    language: LanguageType.polish,
    isAuthenticated: false,
    permissions: [],
}
