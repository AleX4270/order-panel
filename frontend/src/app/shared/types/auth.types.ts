export interface UserLoginCredentials {
    email: string;
    password: string;
}

export interface UserDetailsResponse {
    id: number;
    name: string;
    permissions?: string[];
}
