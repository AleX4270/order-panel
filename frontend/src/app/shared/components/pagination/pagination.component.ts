import { Component, computed, effect, input, output, signal, WritableSignal } from '@angular/core';
import { PAGINATION_PAGE_SIZE, PAGINATION_START_PAGE } from '../../../app.constants';
import { NgSelectModule } from '@ng-select/ng-select';
import { FormsModule } from '@angular/forms';
import { PaginationItem } from '../../types/pagination.types';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'app-pagination',
    imports: [
        NgSelectModule,
        FormsModule,
        CommonModule
    ],
    template: `
        <div class="flex justify-center items-center gap-2">
            <div class="join [&_button]:rounded-sm [&_button]:border-none gap-2">
                <button 
                    class="join-item btn btn-sm btn-square bg-base-100" 
                    [ngClass]="{'cursor-default': isPreviousDisabled()}"
                    (click)="!isPreviousDisabled() && changePage(page() - 1)"
                >
                    &lt;
                </button>
                
                <button class="join-item btn btn-sm btn-square text-base-content" [ngClass]="{'bg-primary/10': page() === 1}" (click)="changePage(1)">{{1}}</button>

                @if(page() >= 4 && totalPages() > 5) {
                    <button class="join-item btn btn-sm btn-square pointer-events-none cursor-default">&hellip;</button>
                }

                @for(position of middlePositions(); track position;) {
                    @let itemValue = getPaginationItemValue(position);

                    <button
                        class="join-item btn btn-sm btn-square" 
                        [ngClass]="{
                            'bg-primary/10': page() === itemValue,
                            'cursor-default': itemValue === null
                        }"
                        (click)="itemValue && changePage(itemValue)"
                    >
                        {{ itemValue }}
                    </button>
                }

                @if(totalPages() >= 5) {
                    @if(totalPages() > 5 && page() <= totalPages() - 3) {
                        <button class="join-item btn btn-sm btn-square pointer-events-none cursor-default">&hellip;</button>
                    }

                    <button class="join-item btn btn-sm btn-square" [ngClass]="{'bg-primary/10': page() === totalPages()}" (click)="changePage(totalPages())">{{ totalPages() }}</button>
                }

                <button 
                    class="join-item btn btn-sm btn-square bg-base-100"
                    [ngClass]="{
                        'cursor-default': isNextDisabled()
                    }"
                    (click)="!isNextDisabled() && changePage(page() + 1)"
                >
                    &gt;
                </button>
            </div>
            <ng-select 
                [items]="pageSizeOptions"
                [searchable]="false"
                [clearable]="false"
                [multiple]="false"
                [ngModel]="pageSize()"
                (change)="changePageSize($event)"
            />
        </div>
    `,
    styles: [``],
})
export class PaginationComponent {
    protected pageSizeOptions = [10,20,50,100] as const;

    public totalItems = input<number>(0);
    public change = output<PaginationItem>();

    protected page: WritableSignal<number> = signal<number>(PAGINATION_START_PAGE);
    protected pageSize: WritableSignal<number> = signal<number>(PAGINATION_PAGE_SIZE);

    constructor() {
        effect(() => {
            let newPageValue = this.page();
            const totalPages = this.totalPages();
            const page = this.page();

            if(page > totalPages) newPageValue = totalPages;
            if(page < 1) newPageValue = 1;

            if(newPageValue !== page) {
                this.page.set(newPageValue);
                queueMicrotask(() => this.emitChange());
            }
        });
    }

    protected totalPages = computed(() => {
        const totalItems = this.totalItems();
        if(totalItems && totalItems > 0) {
            return Math.ceil(totalItems / this.pageSize());
        }

        return 1;
    });

    protected isPreviousDisabled = computed(() => {
        return this.page() <= 1;
    });

    protected isNextDisabled = computed(() => {
        return (this.page() >= this.totalPages());
    })

    protected middlePositions = computed(() => {
        if(this.totalPages() < 4) {
            let positions = [];
            for(let i = 2; i <= this.totalPages(); i++) {
                positions.push(i);
            }
            return positions;
        }

        return [2,3,4];
    });

    protected changePage(newPage: number): void {
        if(newPage < 1 || newPage > this.totalPages()) {
            return;
        }

        this.page.set(newPage);
        this.emitChange();
    }

    protected changePageSize(newPageSize: number): void {
        if(this.pageSize() === newPageSize) return;
        this.pageSize.set(newPageSize);
        this.emitChange();
    }

    protected getPaginationItemValue(position: number): number | null {
        if(this.totalPages() <= 5) {
            return position;
        }

        const isLeftBoundVisible = this.page() < 4;
        const isRightBoundVisible = this.page() > this.totalPages() - 3;

        if(!isLeftBoundVisible && !isRightBoundVisible) {
            switch(position) {
                case 2:
                    return this.page() - 1;
                case 3:
                    return this.page();
                case 4:
                    return this.page() + 1;
            }
        }
        else if(isLeftBoundVisible && !isRightBoundVisible) {
            return position;
        }
        else if(!isLeftBoundVisible && isRightBoundVisible) {
            return this.totalPages() - (5 - position);
        }

        return null;
    }
    
    private emitChange(): void {
        this.change.emit({
            page: this.page(),
            pageSize: this.pageSize(),
        })
    }
}
