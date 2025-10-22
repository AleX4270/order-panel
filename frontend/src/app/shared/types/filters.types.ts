export type FilterType = 'text' | 'select' | 'multi-select' | 'date';

export interface FilterModel<T = any> {
    key: string;
    label: string;
    type: FilterType;
    placeholder?: string;
}
