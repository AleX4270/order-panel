import { Component, computed, effect, input, output, signal, WritableSignal } from '@angular/core';
import { PAGINATION_PAGE_SIZE, PAGINATION_START_PAGE } from '../../../app.constants';
import { NgSelectModule } from '@ng-select/ng-select';
import { FormsModule } from '@angular/forms';

@Component({
    selector: 'app-pagination',
    imports: [
        NgSelectModule,
        FormsModule      
    ],
    template: `
        <div class="d-flex justify-content-center align-items-center gap-2">
            <div>
                <ul class="pagination-list d-flex justify-content-center align-items-center gap-2">
                    <li class="pagination-item" [class.disabled]="isPreviousDisabled()">
                        <button type="button" class="btn btn-link pagination-link" (click)="!isPreviousDisabled() && changePage(page() - 1)">&lt;</button>
                    </li>

                    <li class="pagination-item" [class.active]="page() === 1"><button type="button" class="btn btn-link pagination-link" (click)="changePage(1)">{{1}}</button></li>
                    @if(page() >= 4 && totalPages() > 5) {
                        <li class="pagination-item disabled"><span>&hellip;</span></li>
                    }

                    @for(position of middlePositions(); track position;) {
                        @let itemValue = getPaginationItemValue(position);

                        <li 
                            class="pagination-item" 
                            [class.active]="page() === itemValue"
                            [class.disabled]="itemValue === null"
                        >
                            <button type="button" class="btn btn-link pagination-link" (click)="itemValue && changePage(itemValue)">{{ itemValue }}</button>
                        </li>
                    }

                    @if(totalPages() >= 5) {
                        @if(totalPages() > 5 && page() <= totalPages() - 3) {
                            <li class="pagination-item disabled"><span>&hellip;</span></li>
                        }

                        <li class="pagination-item" [class.active]="page() === totalPages()">
                            <button type="button" class="btn btn-link pagination-link" (click)="changePage(totalPages())">{{ totalPages() }}</button>
                        </li>    
                    }

                    <li 
                        class="pagination-item"
                        [class.disabled]="isNextDisabled()"
                    >
                        <button type="button" class="btn btn-link pagination-link" (click)="!isNextDisabled() && changePage(page() + 1)">&gt;</button>
                    </li>             
                </ul>
            </div>
            <div>
                <ng-select 
                    class="form-field dropdown"
                    [items]="pageSizeOptions"
                    [searchable]="false"
                    [clearable]="false"
                    [multiple]="false"
                    [ngModel]="pageSize()"
                    (change)="changePageSize($event)"
                />
            </div>
        </div>
    `,
    styles: [`
        .pagination-list {
            margin: 12px 0;
            list-style-type: none;
        }

        .pagination-link {
            color: var(--pagination-link-color);
            text-decoration: none;
        }

        .pagination-item {
            border-radius: var(--radius-sm);
            cursor: pointer;
            color: var(--pagination-item-color);

            &.disabled {
                pointer-events: none;
                cursor: default;

                .pagination-link {
                    color: var(--pagination-link-muted-color);
                }
            }

            &:hover {
                background-color: var(--pagination-item-hover-color);
            }

            &.active {
                background-color: var(--pagination-item-active-color);
            }
        }
    `],
})
export class PaginationComponent {
    protected pageSizeOptions = [10,20,50,100] as const;

    public totalItems = input<number>(0);

    public pageChange = output<number>();
    public pageSizeChange = output<number>();

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
                queueMicrotask(() => this.pageChange.emit(this.page()));
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
        this.pageChange.emit(newPage);
    }

    protected changePageSize(newPageSize: number): void {
        if(this.pageSize() === newPageSize) return;
        this.pageSize.set(newPageSize);
        this.pageSizeChange.emit(this.pageSize());
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
}
