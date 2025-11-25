export interface SortItem {
    sortDir: SortDir;
    sortColumn: string;
}

export type SortDir = 'asc' | 'desc';