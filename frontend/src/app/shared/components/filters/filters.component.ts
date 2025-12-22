import { Component, effect, inject, input, InputSignal, output, signal, WritableSignal } from '@angular/core';
import { FilterModel, FilterOption } from '../../types/filters.types';
import { NgSelectComponent } from '@ng-select/ng-select';
import { IFiltersStrategy } from '../../interfaces/filters/filters-strategy.interface';
import { FilterType } from '../../enums/filter-type.enum';
import { FiltersStrategyFactory } from '../../factories/filters-strategy.factory';
import { TranslatePipe } from '@ngx-translate/core';
import { FormsModule } from '@angular/forms';
import { debounceTime, Subject } from 'rxjs';

@Component({
    selector: 'app-filters',
    imports: [
        NgSelectComponent,
        TranslatePipe,
        FormsModule
    ],
    template: `
        <div class="w-full flex flex-col gap-y-3 sm:flex-row sm:flex-wrap sm:gap-4 justify-center sm:justify-start items-start">
            @for(filter of filters(); track filter) {
                @switch(filter.type) {
                    @case ('text') {
                        <div class="w-full sm:max-w-xs mt-3 sm:mt-1">
                            <label [for]="filter.key" class="floating-label">
                                <span>{{ filter.label | translate}}</span>
                                <input
                                    class="input placeholder:opacity-50 w-full text-xs"
                                    type="text"
                                    [id]="filter.key"
                                    [name]="filter.key"
                                    [placeholder]="(filter.placeholder ?? '') | translate"
                                    (input)="onFilterValueChange($event.target.value, filter)"
                                />
                            </label>
                        </div>
                    }
                    @case ('multi-select') {
                        <div class="w-full sm:max-w-xs mt-3 sm:mt-1">
                            <label [for]="filter.key" class="floating-label">
                                <span>{{ filter.label | translate}}</span>
                                <ng-select 
                                    [items]="filtersDataMap()[filter.key] ?? []"
                                    bindValue="id"
                                    bindLabel="name"
                                    [multiple]="true"
                                    [placeholder]="(filter.placeholder ?? '') | translate"
                                    (search)="onFilterDataSearch($event.term, filter)"
                                    (change)="onFilterValueChange($event, filter)"
                                />
                            </label>
                        </div>
                    }
                    
                    @case ('date') {
                        <div class="w-full sm:max-w-3xs mt-3 sm:mt-1">
                            <label [for]="filter.key" class="floating-label">
                                <span>{{ filter.label | translate}}</span>
                                <input
                                    class="input placeholder:opacity-50 w-full text-xs"
                                    type="date"
                                    [id]="filter.key"
                                    [name]="filter.key"
                                    (change)="onFilterValueChange($event.target.value, filter)"
                                />
                            </label>
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
    public filtersChange = output<Partial<Record<string, string | number[] | null>>>();

    private strategy!: IFiltersStrategy | null;
    protected readonly filters: WritableSignal<FilterModel[]> = signal<FilterModel[]>([]);
    protected filtersDataMap: WritableSignal<Partial<Record<string, FilterOption[]>>> = signal<Partial<Record<string, FilterOption[]>>>({});
    protected filterValues: Partial<Record<string, string | number[] | null>> = {};

    protected textInput$ = new Subject<{value: string, filter: FilterModel}>();

    constructor() {
        effect(() => {
            if(this.type()) {
                this.strategy = this.strategyFactory.create(this.type());

                if(this.strategy != null) {
                    const filters = this.strategy.getFilters();
                    this.filters.set(filters);

                    for(let filter of this.filters()) {
                        this.onFilterDataSearch('', filter);
                    }
                }
            }
        });

        this.textInput$.pipe(
            debounceTime(500),
        )
        .subscribe(({value, filter}) => {
            this.filterValues[filter.key] = value;
            this.emitFilterValues();
        });
    }

    protected onFilterDataSearch(term: string, filter: FilterModel): void {
        if(!filter.loader) {
            return;
        }

        filter.loader(term).subscribe({
            next: (res) => {
                const newFieldData = res.filter((item) => !this.filtersDataMap()[filter.key]?.some((existingItem) => existingItem.id == item.id));

                this.filtersDataMap.update((currData) => ({
                    ...currData,
                    [filter.key]: [ ...this.filtersDataMap()[filter.key] ?? [], ...newFieldData ]
                }));
            },
            error: (err) => {
                console.error(err);
            }
        });
    }

    protected onFilterValueChange(value: string | FilterOption[] | null, filter: FilterModel): void {
        let selectedValue = null;

        if(filter.type === 'multi-select' && Array.isArray(value)) {
            selectedValue = value.map((item) => item.id);
        }
        else if(filter.type === 'text') {
            this.textInput$.next({value: String(value), filter: filter});
            return;
        }
        else {
            selectedValue = String(value);
        }
        
        this.filterValues[filter.key] = selectedValue;
        this.emitFilterValues();
    }

    protected emitFilterValues(): void {
        Object.keys(this.filterValues).forEach((key) => {
            if(this.isFilterValueEmpty(this.filterValues[key])) {
                delete this.filterValues[key];
            }
        })

        this.filtersChange.emit(this.filterValues);
    }

    private isFilterValueEmpty(value: any): boolean {
        if(value === null) return true;
        if(value === '') return true;
        if(Array.isArray(value) && value.length == 0) return true;
        
        return false;
    }
}
