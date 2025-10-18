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
                <ul class="pagination-list d-flex justify-content-center align-items-center">
                    <li 
                        class="pagination-item"
                        [class.disabled]="isPreviousDisabled()"
                    >
                        <a (click)="!isPreviousDisabled() && changePage(page() - 1)">&lt;</a>
                    </li>

                    @for(position of [1,2,3,4,5]; track position;) {
                        @let itemValue = getPaginationItemValue(position);

                        <li 
                            class="pagination-item" 
                            [class.active]="page() === itemValue"
                            [class.disabled]="itemValue === null"
                        >
                            <a (click)="itemValue && changePage(itemValue)">
                                {{ itemValue ?? '...' }}
                            </a>
                        </li>
                    }
                    
                    @if(totalPages() > 5) {
                        <li class="pagination-item" [class.active]="page() === totalPages()"><a (click)="changePage(totalPages())">{{ totalPages() }}</a></li>
                    }

                    <li 
                        class="pagination-item"
                        [class.disabled]="isNextDisabled()"
                    >
                        <a (click)="!isNextDisabled() && changePage(page() + 1)">&gt;</a>
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

        .pagination-item {
            padding: 8px 12px;
            border-radius: var(--small-border-radius);
            cursor: pointer;

            &.disabled {
                pointer-events: none;
                cursor: default;

                a {
                    color: var(--text-muted-color);
                }
            }

            &:hover {
                background-color: #eaf3ff;
            }

            &.active {
                background-color: #dce9ff
            }

            a {
                text-decoration: none;
            }
        }
    `],
})
export class PaginationComponent {
    public pageSize = input<number>(10);
    // public totalItems = input.required<number>();
    public totalItems = input<number>(70);

    public pageChange = output<number>();
    public pageSizeChange = output<number>();

    protected page: WritableSignal<number> = signal<number>(PAGINATION_START_PAGE);

    protected totalPages = computed(() => {
        return Math.ceil(this.totalItems() / this.pageSize());
    });

    protected isPreviousDisabled = computed(() => {
        return this.page() <= 1;
    });

    protected isNextDisabled = computed(() => {
        return this.page() >= this.totalPages();
    })

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

        let pageValue = this.totalPages() - (this.totalPages() - position);
        const isMiddle = this.page() >= 4 && this.page() <= this.totalPages() - 3;

        switch(position) {
            case 1:
                return pageValue;
            case 2:
                return (this.page() >= 4) ? null : pageValue;
            case 3:
                return isMiddle ? this.page() - 1 : pageValue;
            case 4:
                return isMiddle ? this.page() : pageValue;
            case 5:
                return (this.page() <= this.totalPages() - 3) ? null : isMiddle ? this.page() + 1 : pageValue;
            default:
                return pageValue;
        }
    }
}
