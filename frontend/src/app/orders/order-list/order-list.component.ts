import { Component, inject, OnInit, signal, ViewChild, WritableSignal } from '@angular/core';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { ListTableComponent } from '../../shared/components/list-table/list-table.component';
import { ExpansionState, TileType, ToastType } from '../../shared/enums/enums';
import { Priority } from '../../shared/enums/priority.enum';
import { TileComponent } from "../../shared/components/tile/tile.component";
import { NgIcon, provideIcons } from '@ng-icons/core';
import { faEye, faPenToSquare, faTrashCan } from '@ng-icons/font-awesome/regular';
import { ColorLabelComponent } from '../../shared/components/color-label/color-label.component';
import { CardComponent } from "../../shared/components/card/card.component";
import { PaginationComponent } from '../../shared/components/pagination/pagination.component';
import { FiltersComponent } from '../../shared/components/filters/filters.component';
import { FilterType } from '../../shared/enums/filter-type.enum';
import { OrderFormModalComponent } from "../order-form-modal/order-form-modal.component";
import { OrderService } from '../../shared/services/api/order/order.service';
import { DatePipe, NgClass } from '@angular/common';
import { OrderFilterParams, OrderItem } from '../../shared/types/order.types';
import { PaginationItem } from '../../shared/types/pagination.types';
import { Status } from '../../shared/enums/status.enum';
import { SortItem } from '../../shared/types/sort.types';
import { PromptModalService } from '../../shared/services/prompt-modal/prompt-modal.service';
import { ToastService } from '../../shared/services/toast/toast.service';

@Component({
    selector: 'app-order-list',
    imports: [
    TranslatePipe,
    ListTableComponent,
    TileComponent,
    NgIcon,
    ColorLabelComponent,
    CardComponent,
    PaginationComponent,
    FiltersComponent,
    OrderFormModalComponent,
    DatePipe,
    NgClass
],
    providers: [provideIcons({faEye, faPenToSquare, faTrashCan})],
    template: `
        <div class="row order-list-header">
            <div class="col-12">
                <div class="row">
                    <div class="col-6">
                        <h3>{{'orderList.header' | translate}}</h3>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <app-card overflowType="visible">
                            <app-filters 
                                [type]="filterType.orderListFilters"
                                (filtersChange)="onOrderFiltersChange($event)"
                            />        
                        </app-card>
                    </div>
                </div>
            </div> 
        </div>

        <div class="row order-list-info mt-5">
            <div class="col-8">
                <h5>{{ ('orderList.orders' | translate) + ' (' + ordersCount() + ')'}}</h5>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <span class="text-muted">{{'orderList.legend' | translate}}:</span>
                    <div class="d-flex gap-3">
                        <app-color-label color="var(--order-list-default-row-background-color)" [label]="'orderList.inProgress' | translate"></app-color-label>
                        <app-color-label color="var(--order-list-overdue-row-background-color)" [label]="'orderList.overdue' | translate"></app-color-label>
                        <app-color-label color="var(--order-list-completed-row-background-color)" [label]="'orderList.completed' | translate"></app-color-label>
                    </div>
                </div>
            </div>
            <div class="col-4 text-end">
                <button class="btn btn-sm btn-primary" (click)="showOrderFormModal()" >+ {{'orderList.addNewOrder' | translate}}</button>
            </div>
        </div>

        <div class="row order-list-table mt-2">
            <div class="col-12">
                <app-list-table
                    [defineTableRowsExternally]="true"
                    [data]="orders()"
                >
                    <ng-template #headers>
                        <th class="cursor-pointer" (click)="onOrderSortChange('orderNumber')">{{'orderListTable.orderNo' | translate}}</th>
                        <th class="cursor-pointer" (click)="onOrderSortChange('address')">{{'orderListTable.address' | translate}}</th>
                        <th class="cursor-pointer" (click)="onOrderSortChange('priority')">{{'orderListTable.priority' | translate}}</th>
                        <th class="cursor-pointer" (click)="onOrderSortChange('dateCreated')">{{'orderListTable.dateCreated' | translate}}</th>
                        <th class="cursor-pointer" (click)="onOrderSortChange('dateDeadline')">{{'orderListTable.dateDeadline' | translate}}</th>
                        <th class="cursor-pointer" (click)="onOrderSortChange('remarks')">{{'orderListTable.remarks' | translate}}</th>
                        <th>{{'orderListTable.actions' | translate}}</th>
                    </ng-template>

                    <ng-template #rows let-item>
                        <tr 
                            class="list-row"
                            [class.completed-row]="item.statusSymbol === status.completed"
                            [class.overdue-row]="item.isOverdue"
                        >
                            <td class="fw-semibold">{{ '#' + item.id }}</td>
                            <td>
                                <div class="address d-flex flex-column">
                                    <span class="city-name">{{item.cityName}}</span>
                                    <span class="address-label text-muted">{{item.address}}</span>
                                </div>
                            </td>
                            <td>
                                <app-tile [type]="getPriorityTileType(item.prioritySymbol)">
                                    {{ item.priorityName }}
                                </app-tile>
                            </td>
                            <td>{{ item.dateCreated | date:'dd-MM-yyyy' }}</td>
                            <td>{{ item.dateDeadline | date:'dd-MM-yyyy'}}</td>
                            <td class="text-secondary">{{ item.remarks }}</td>
                            <td>
                                <div class="d-flex gap-3">
                                    <ng-icon
                                        class="item-pressable text-dark"
                                        name="faEye"
                                        size="20px"
                                        (click)="toggleItemDetailsExpansion(item.id)"
                                    ></ng-icon>

                                    <ng-icon
                                        class="item-pressable text-primary"
                                        name="faPenToSquare"
                                        size="20px"
                                        (click)="showOrderFormModal(item.id)"
                                    ></ng-icon>

                                    <ng-icon
                                        class="item-pressable text-danger"
                                        name="faTrashCan"
                                        size="20px"
                                        (click)="showOrderDeletePromptModal(item.id)"
                                    ></ng-icon>
                                </div>
                            </td>
                        </tr>

                        <tr [class.d-none]="!hasVisibleDetails(item.id)">
                            <td colspan="7" class="p-0">
                                <div class="expandable-content order-details p-3">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="details-header text-primary">{{ 'orderDetails.header' | translate}}</h6>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'orderDetails.orderNo' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ '#' + item.id }}</span>
                                            </div>
                                        </div>
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'orderDetails.address' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ item.address + ', ' + (item.postalCode ? (item.postalCode + ', ') : '') + item.cityName }}</span>
                                            </div>
                                        </div>
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'orderDetails.phoneNumber' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ item.phoneNumber }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'orderDetails.priority' | translate}}</span>
                                            <div class="details-value">
                                                <app-tile [type]="getPriorityTileType(item.prioritySymbol)">
                                                    {{ item.priorityName }}
                                                </app-tile>
                                            </div>
                                        </div>
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'orderDetails.status' | translate}}</span>
                                            <div class="details-value">
                                                <app-tile [type]="getStatusTileType(item.statusSymbol)">
                                                    {{ item.statusName }}
                                                </app-tile>
                                            </div>
                                        </div>
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'orderDetails.isOverdue' | translate}}</span>
                                            <div class="details-value">
                                                <app-tile [type]="item.isOverdue ? tileType.danger : tileType.success">
                                                    {{ (item.isOverdue ? 'basic.yes' : 'basic.no') | translate}}
                                                </app-tile>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'orderDetails.dateCreated' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ item.dateCreated | date:'dd-MM-yyyy' }}</span>
                                            </div>
                                        </div>
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'orderDetails.dateDeadline' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ item.dateDeadline | date:'dd-MM-yyyy' }}</span>
                                            </div>
                                        </div>
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'orderDetails.dateCompleted' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ (item.dateCompleted | date:'dd-MM-yyyy') ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'orderDetails.remarks' | translate}}</span>
                                            <div class="details-value">
                                                <span class="text-muted">{{ item.remarks ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </ng-template>
                </app-list-table>
            </div>
        </div>

        <div class="row order-list-pagination mt-5 pb-4">
            <div class="col-12">
                <app-card [isContentCentered]="true" overflowType="visible">
                    <app-pagination [totalItems]="ordersCount()" (change)="onOrderPaginationChange($event)"></app-pagination>
                </app-card>
            </div>
        </div>

        <app-order-form-modal #orderFormModal (orderSaved)="loadOrders()" />
    `,
    styles: [`
        .order-details {
            .details-header {
                color: var(--order-list-header-text-color);
            }

            .details-label {
                font-size: var(--font-size-xs);
                font-weight: var(--font-weight-light);
            }

            .details-value {
                font-size: var(--font-size-xs);
            }
        }
        
        .list-row {
            background-color: var(--order-list-default-row-background-color);
            border-bottom: 1px solid var(--order-list-row-border-color);

            &:hover {
                background-color: var(--order-list-hovered-row-background-color);
            }

            td {
                font-size: var(--font-size-xs);
                padding: 1.1rem 0 1.1rem 0.75rem;
            }
        }

        .overdue-row {
            background-color: var(--order-list-overdue-row-background-color);
        }

        .completed-row {
            background-color: var(--order-list-completed-row-background-color);
        }

        .address {
            .city-name {
                font-size: var(--font-size-sm);
            }

            .address-label {
                font-size: var(--font-size-xs);
                font-weight: var(--font-weight-light);
            }
        }
    `]
})
export class OrderListComponent implements OnInit {
    @ViewChild('orderFormModal') orderFormModal!: OrderFormModalComponent;

    private readonly orderService = inject(OrderService);
    private readonly promptModalService = inject(PromptModalService);
    private readonly translateService = inject(TranslateService);
    private readonly toastService = inject(ToastService);

    protected readonly filterType = FilterType;
    protected readonly status = Status;
    protected readonly tileType = TileType;

    protected expansionState = ExpansionState;
    protected itemDetailsExpansionState: Partial<Record<number, ExpansionState>> = {};

    protected orders: WritableSignal<OrderItem[]> = signal<OrderItem[]>([]);
    protected ordersCount: WritableSignal<number> = signal<number>(0);

    protected orderFilterValues: Partial<Record<string, string | number[] | null>> = {};
    protected orderPaginationValues: PaginationItem | null = null;
    protected orderSortValues: WritableSignal<SortItem | null> = signal<SortItem | null>(null);

    ngOnInit(): void {
        this.loadOrders();
    }

    protected hasVisibleDetails(itemId: number): boolean {
        return (this.itemDetailsExpansionState[itemId] === ExpansionState.expanded) || false;
    }

    protected toggleItemDetailsExpansion(itemId: number): void {
        if (this.itemDetailsExpansionState[itemId] === ExpansionState.expanded) {
            this.itemDetailsExpansionState[itemId] = ExpansionState.collapsed;
        } else {
            this.itemDetailsExpansionState[itemId] = ExpansionState.expanded;
        }
    }

    protected getPriorityTileType(type: Priority): TileType {
        return type === Priority.high
            ? TileType.danger
            : TileType.secondary;
    }

    protected getStatusTileType(type: Status): TileType {
        switch(type) {
            case Status.in_progress:
                return TileType.warning;
            case Status.completed:
                return TileType.success;
            default:
                return TileType.primary;
        }
    }
    
    protected showOrderFormModal(id?: number): void {
        this.orderFormModal.showForm(id);
    }

    protected showOrderDeletePromptModal(id: number): void {
        this.promptModalService.openModal({
            title: this.translateService.instant('orderDeletePromptModal.title'),
            message: this.translateService.instant('orderDeletePromptModal.message'),
            handler: () => {
                this.deleteOrder(id);
            }
        });
    }

    protected deleteOrder(id: number): void {
        this.orderService.delete(id).subscribe({
            next: () => {
                this.toastService.show(
                    this.translateService.instant('orderList.deleteSuccess'),
                    ToastType.success,
                );
                this.loadOrders();
            },
            error: (err) => {
                console.error(err);
                this.toastService.show(
                    this.translateService.instant('orderList.deleteError'),
                    ToastType.danger,
                );
            }
        })
    }

    protected loadOrders(): void {
        let params = {} as OrderFilterParams;

        if(Object.keys(this.orderFilterValues).length > 0) {
            Object.keys(this.orderFilterValues).forEach((key) => {
                params = {
                    ...params,
                    [key]: this.orderFilterValues[key],
                }
            })
        }

        if(this.orderPaginationValues?.page) {
            params.page = this.orderPaginationValues.page;
        }

        if(this.orderPaginationValues?.pageSize) {
            params.pageSize = this.orderPaginationValues.pageSize;
        }

        if(this.orderSortValues()?.sortColumn) {
            params.sortColumn = this.orderSortValues()?.sortColumn;
        }

        if(this.orderSortValues()?.sortDir) {
            params.sortDir = this.orderSortValues()?.sortDir;
        }

        this.orderService.index(params).subscribe({
            next: (res) => {
                this.orders.set(res.data?.items ?? []);
                this.ordersCount.set(res.data?.count ?? 0);
            },
            error: (err) => {
                console.error(err);
            },
        });
    }

    protected onOrderFiltersChange(filterValues: Partial<Record<string, string | number[] | null>>): void {
        this.orderFilterValues = filterValues;
        this.loadOrders();
    }

    protected onOrderPaginationChange(paginationValues: PaginationItem): void {
        this.orderPaginationValues = paginationValues;
        this.loadOrders();
    }

    protected onOrderSortChange(column: string): void {
        if(this.orderSortValues()?.sortColumn !== column) {
            this.orderSortValues.set({
                sortColumn: column,
                sortDir: 'desc',
            });
        }

        if(this.orderSortValues()?.sortDir === 'asc') {
            this.orderSortValues.set({
                sortColumn: column,
                sortDir: 'desc',
            });
        }
        else {
            this.orderSortValues.set({
                sortColumn: column,
                sortDir: 'asc',
            });
        }

        this.loadOrders();
    }
}
