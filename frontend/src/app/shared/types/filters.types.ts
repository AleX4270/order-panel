import { Observable } from "rxjs";

export type FilterFieldType = 'text' | 'select' | 'multi-select' | 'date';

export interface FilterOption {
    id: number;
    label: string;
}

export interface FilterModel {
    key: string;
    label: string;
    type: FilterFieldType;
    placeholder?: string;
    loader?: Observable<FilterOption[]>;
}
