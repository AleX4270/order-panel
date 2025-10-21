import { Component, input, InputSignal, output } from '@angular/core';
import { FilterModel } from '../../types/filters.types';

@Component({
    selector: 'app-filters',
    imports: [],
    template: ``,
    styles: [``]
})
export class FiltersComponent {
    public filters: InputSignal<FilterModel[]> = input.required<FilterModel[]>();
    public filtersChange = output<Map<string, string>>();
}
