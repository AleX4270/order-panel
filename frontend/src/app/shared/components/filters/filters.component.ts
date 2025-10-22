import { Component, input, InputSignal, output } from '@angular/core';
import { FilterModel } from '../../types/filters.types';
import { NgSelectComponent } from '@ng-select/ng-select';

@Component({
    selector: 'app-filters',
    imports: [
        NgSelectComponent
    ],
    template: `
        <div class="row w-100 p-4 d-flex justify-content-start align-items-start">
            @for(filter of filters(); track filter) {
                @switch(filter.type) {
                    @case ('text') {
                        <div class="form-group filter-control col-3">
                            <label [for]="filter.key">{{ filter.label }}</label>
                            <input
                                class="form-control"
                                type="text"
                                [id]="filter.key"
                                [name]="filter.key"
                            />
                        </div>
                    }
                    @case ('multi-select') {
                        <div class="form-group filter-control col-3">
                            <label [for]="filter.key">{{ filter.label }}</label>
                            <ng-select 
                                class="form-control"
                                [items]="[1,2,3]"
                                [searchable]="false"
                                [multiple]="true"
                            />
                        </div>
                    }
                    
                    @case ('date') {
                        <div class="form-group filter-control col-3">
                            <label [for]="filter.key">{{ filter.label }}</label>
                            <!-- TODO Max date cap -->
                            <input
                                class="form-control"
                                type="date"
                                [id]="filter.key"
                                [name]="filter.key"
                            />
                        </div>
                    }
                }
            }
        </div>
    `,
    styles: [`
        label {
            font-size: 0.9rem;
        }

        .filter-control {
            display: flex;
            flex-direction: column;

            input, ng-select {
                margin-top: 5px;
            }
        }
    `]
})
export class FiltersComponent {
    public filters: InputSignal<FilterModel[]> = input.required<FilterModel[]>();
    public filtersChange = output<Map<string, string>>();
}
