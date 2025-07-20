export interface ApiResponse<T> {
    data: T | null;
    message?: string;
    timestamp: string;
}
