import { Component, computed, effect, input, output, signal, WritableSignal } from '@angular/core';
import { PAGINATION_START_PAGE } from '../../../app.constants';

@Component({
    selector: 'app-pagination',
    imports: [],
    template: `
        <div class="d-flex justify-content-center align-items-center">
            <div>
                <!-- TODO: Page jump -->
            </div>
            <div>
                <ul class="pagination-list d-flex justify-content-center align-items-center gap-2">
                    <li class="pagination-item" [class.disabled]="isPreviousDisabled()">
                        <button type="button" class="btn btn-sm btn-link pagination-link" (click)="!isPreviousDisabled() && changePage(page() - 1)">&lt;</button>
                    </li>

                    <li class="pagination-item" [class.active]="page() === 1"><button type="button" class="btn btn-sm btn-link pagination-link" (click)="changePage(1)">{{1}}</button></li>
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
                            <button type="button" class="btn btn-sm btn-link pagination-link" (click)="itemValue && changePage(itemValue)">{{ itemValue }}</button>
                        </li>
                    }

                    @if(totalPages() >= 5) {
                        @if(totalPages() > 5 && page() <= totalPages() - 3) {
                            <li class="pagination-item disabled"><span>&hellip;</span></li>
                        }

                        <li class="pagination-item" [class.active]="page() === totalPages()">
                            <button type="button" class="btn btn-sm btn-link pagination-link" (click)="changePage(totalPages())">{{ totalPages() }}</button>    
                        </li>    
                    }

                    <li 
                        class="pagination-item"
                        [class.disabled]="isNextDisabled()"
                    >
                        <button type="button" class="btn btn-sm btn-link pagination-link" (click)="!isNextDisabled() && changePage(page() + 1)">&gt;</button>
                    </li>             
                </ul>
            </div>
            <div>
                <!-- TODO: Page Size Dropdown -->
            </div>
        </div>
    `,
    styles: [`
        .pagination-list {
            margin: 12px 0;
            list-style-type: none;
        }

        .pagination-link {
            color: var(--text-primary-color);
            text-decoration: none;
        }

        .pagination-item {
            border-radius: var(--small-border-radius);
            cursor: pointer;
            color: var(--text-primary-color);

            &.disabled {
                pointer-events: none;
                cursor: default;

                .pagination-link {
                    color: var(--text-muted-color);
                }
            }

            &:hover {
                background-color: #eaf3ff;
            }

            &.active {
                background-color: #dce9ff;
            }
        }
    `],
})
export class PaginationComponent {
    public pageSize = input<number>(10);
    public totalItems = input<number>(0);

    public pageChange = output<number>();
    public pageSizeChange = output<number>();

    protected page: WritableSignal<number> = signal<number>(PAGINATION_START_PAGE);

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
        if(this.totalItems() > 0) {
            return Math.ceil(this.totalItems() / this.pageSize());
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
