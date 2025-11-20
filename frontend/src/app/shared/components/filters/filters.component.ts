import { Component, effect, inject, input, InputSignal, output, signal, WritableSignal } from '@angular/core';
import { FilterModel, FilterOption } from '../../types/filters.types';
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
                                [items]="filtersDataMap()[filter.key] ?? []"
                                [multiple]="true"
                                [placeholder]="(filter.placeholder ?? '') | translate"
                                (search)="onFilterDataSearch($event.term, filter)"
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
            font-size: var(--font-size-sm);
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
    protected filtersDataMap: WritableSignal<Partial<Record<string, FilterOption[]>>> = signal<Partial<Record<string, FilterOption[]>>>({});

    constructor() {
        effect(() => {
            if(this.type()) {
                this.strategy = this.strategyFactory.create(this.type());

                if(this.strategy != null) {
                    const filters = this.strategy.getFilters();
                    this.filters.set(filters);
                }
            }
        });
    }

    protected onFilterDataSearch(term: string, filter: FilterModel): void {
        if(!filter.loader) {
            console.error('No filter data loader found');
            return;
        }

        filter.loader(term).subscribe({
            next: (res) => {
                const newData = res.filter((item) => !this.filtersDataMap()[filter.key]?.some((existingItem) => existingItem.id == item.id));

                
            },
            error: (err) => {
                console.error(err);
            }
        });
    }
}
