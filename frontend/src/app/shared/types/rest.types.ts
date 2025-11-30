export interface ApiResponse<T> {
    data: T | null;
    message?: string;
    timestamp: string;
}

export interface BaseFilterParams {
    page?: number;
    pageSize?: number;
    sortColumn?: string;
    sortDir?: string;
}

export interface IndexResponse<T> {
    items: T[];
    count: number;
}