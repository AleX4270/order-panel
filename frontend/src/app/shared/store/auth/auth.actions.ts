import { User } from "../../types/user.types";

export class LoginUser {
    static readonly type = '[Auth] Login User';
    constructor(public user: User) {}
}

export class LogoutUser {
    static readonly type = '[Auth] Logout';
    constructor() {}
}
