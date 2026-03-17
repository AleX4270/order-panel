import { LanguageType } from "../../enums/enums";
import { User } from "../../types/user.types";

export interface UserStateModel extends User {}

export const initialUserState: UserStateModel = {
    id: 0,
    username: '',
    language: LanguageType.polish,
    isAuthenticated: false,
    permissions: [],
}
