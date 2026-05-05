import { Observable } from "rxjs";

export type FilterFieldType = 'text' | 'select' | 'multi-select' | 'date' | 'number';

export interface FilterOption {
    id: number;
    name: string;
}

export interface FilterModel {
    key: string;
    label: string;
    type: FilterFieldType;
    placeholder?: string;
    loader?: (term?: string) => Observable<FilterOption[]>;
    validate?: (value: string | FilterOption[] | null) => boolean;
}
