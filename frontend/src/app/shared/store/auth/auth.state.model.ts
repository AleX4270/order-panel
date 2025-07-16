import { User } from "../../types/user.types";

export interface AuthStateModel {
    user: User | null;
    isAuthenticated: boolean;
}
