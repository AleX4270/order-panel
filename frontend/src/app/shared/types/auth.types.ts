export interface UserLoginCredentials {
    email: string;
    password: string;
}

export interface UserDetailsResponse {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    created_at: string;
    updated_at: string;
}
