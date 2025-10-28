import { Component, effect, inject, input, InputSignal, output, signal, WritableSignal } from '@angular/core';
import { FilterModel } from '../../types/filters.types';
import { NgSelectComponent } from '@ng-select/ng-select';
import { IFiltersStrategy } from '../../interfaces/filters/filters-strategy.interface';
import { FilterType } from '../../enums/filter-type.enum';
import { FiltersStrategyFactory } from '../../factories/filters-strategy.factory';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-filters',
    imports: [
        NgSelectComponent,
        TranslatePipe,
    ],
    template: `
        <div class="row w-100 p-4 d-flex justify-content-start align-items-start">
            @for(filter of filters(); track filter) {
                @switch(filter.type) {
                    @case ('text') {
                        <div class="form-group filter-control col-3">
                            <label [for]="filter.key">{{ filter.label | translate}}</label>
                            <input
                                class="form-field input"
                                type="text"
                                [id]="filter.key"
                                [name]="filter.key"
                                [placeholder]="(filter.placeholder ?? '') | translate"
                            />
                        </div>
                    }
                    @case ('multi-select') {
                        <div class="form-group filter-control col-2">
                            <label [for]="filter.key">{{ filter.label | translate}}</label>
                            <ng-select 
                                class="form-field dropdown"
                                [items]="[1,2,3]"
                                [searchable]="false"
                                [multiple]="true"
                                [placeholder]="(filter.placeholder ?? '') | translate"
                            />
                        </div>
                    }
                    
                    @case ('date') {
                        <div class="form-group filter-control col-2">
                            <label [for]="filter.key">{{ filter.label | translate}}</label>
                            <!-- TODO Max date cap -->
                            <input
                                class="form-field input"
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
            margin-top: 10px;

            input, ng-select {
                margin-top: 5px;
            }
        }
    `]
})
export class FiltersComponent {
    private readonly strategyFactory: FiltersStrategyFactory = inject(FiltersStrategyFactory);

    public type: InputSignal<FilterType> = input.required<FilterType>();
    public filtersChange = output<Map<string, string>>();

    private strategy!: IFiltersStrategy | null;
    protected readonly filters: WritableSignal<FilterModel[]> = signal<FilterModel[]>([]);

    constructor() {
        effect(() => {
            if(this.type()) {
                this.strategy = this.strategyFactory.create(this.type());

                if(this.strategy != null) {
                    // TODO
                    const filters = this.strategy.getFilters();
                    this.filters.set(filters);
                }
            }
        });
    }
}
