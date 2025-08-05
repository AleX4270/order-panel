import { LanguageType } from "../../enums/enums";
import { User } from "../../types/user.types";

export class LoginUser {
    static readonly type = '[User] Login User';
    constructor(public data: User) {}
}

export class LogoutUser {
    static readonly type = '[User] Logout';
    constructor() {}
}

export class SetUserLanguage {
    static readonly type = '[User] Set User Language';
    constructor(public languageSymbol: LanguageType) {}
}
