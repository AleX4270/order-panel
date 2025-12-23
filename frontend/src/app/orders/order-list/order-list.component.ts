import { Component, inject, OnInit, signal, ViewChild, WritableSignal } from '@angular/core';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { ListTableComponent } from '../../shared/components/list-table/list-table.component';
import { ExpansionState, ToastType } from '../../shared/enums/enums';
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
import { TileType } from '../../shared/types/tile.types';
import { ButtonComponent } from "../../shared/components/button/button.component";

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
    NgClass,
    ButtonComponent
],
    providers: [provideIcons({faEye, faPenToSquare, faTrashCan})],
    template: `
        <div class="w-full order-list-header">
            <h1 class="font-semibold text-2xl mb-5">{{'orderList.header' | translate}}</h1>
            <app-card overflowType="visible" [title]="'basic.filters' | translate">
                <app-filters 
                    [type]="filterType.orderListFilters"
                    (filtersChange)="onOrderFiltersChange($event)"
                />        
            </app-card>
        </div>

        <div class="w-full mt-5 flex justify-between items-end">
            <div class="flex flex-col">
                <h2 class="font-medium text-xl">{{ ('orderList.orders' | translate) + ' (' + ordersCount() + ')'}}</h2>
                <div class="flex flex-col sm:flex-row sm:items-center sm:mt-3">
                    <span class="text-neutral/45 font-light text-sm">{{'orderList.legend' | translate}}:</span>
                    <div class="flex gap-2 text-xs mt-2 sm:mt-0 sm:ms-2 font-light">
                        <app-color-label colorClass="bg-base-100" [label]="'orderList.inProgress' | translate"></app-color-label>
                        <app-color-label colorClass="bg-error/15" [label]="'orderList.overdue' | translate"></app-color-label>
                        <app-color-label colorClass="bg-neutral/9" [label]="'orderList.completed' | translate"></app-color-label>
                    </div>
                </div>
            </div>
            <app-button classList="btn btn-sm btn-primary md:btn-md" (click)="showOrderFormModal()">{{'orderList.addNewOrder' | translate}}</app-button>
        </div>

        <div class="w-full mt-2">
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
                        class="bg-base-100 hover:bg-base-300/70 [&_td]:text-xs p-1"
                        [ngClass]="{
                            'bg-neutral/9': item.statusSymbol === status.completed,
                            'bg-error/15': item.isOverdue && item.statusSymbol !== status.completed
                        }"
                    >
                        <td class="font-normal"><span>{{ '#' + item.id }}</span></td>
                        <td>
                            <div class="flex flex-col">
                                <span class="text-xs">{{item.cityName}}</span>
                                <span class="text-base-content/70 font-light mt-1">{{item.address}}</span>
                            </div>
                        </td>
                        <td>
                            <app-tile [type]="getPriorityTileType(item.prioritySymbol)" [isSoft]="true" [isOutlined]="true">
                                <span>{{ item.priorityName }}</span>
                            </app-tile>
                        </td>
                        <td><span>{{ item.dateCreated | date:'dd-MM-yyyy' }}</span></td>
                        <td><span>{{ item.dateDeadline | date:'dd-MM-yyyy'}}</span></td>
                        <td class="text-base-content/60 font-light">{{ item.remarks }}</td>
                        <td>
                            <div class="flex gap-3">
                                <ng-icon
                                    class="item-pressable"
                                    name="faEye"
                                    size="18px"
                                    (click)="toggleItemDetailsExpansion(item.id)"
                                ></ng-icon>

                                <ng-icon
                                    class="item-pressable [&>svg]:fill-primary"
                                    name="faPenToSquare"
                                    size="18px"
                                    (click)="showOrderFormModal(item.id)"
                                ></ng-icon>

                                <ng-icon
                                    class="item-pressable [&>svg]:fill-error"
                                    name="faTrashCan"
                                    size="18px"
                                    (click)="showOrderDeletePromptModal(item.id)"
                                ></ng-icon>
                            </div>
                        </td>
                    </tr>

                    <!-- TODO: This needs to be a standalone component -->
                    <tr [class.hidden]="!hasVisibleDetails(item.id)">
                        <td colspan="7" class="p-0">
                            <div class="p-3">
                                <div class="w-full">
                                    <h6 class="text-primary text-sm">{{ 'orderDetails.header' | translate}}</h6>
                                </div>

                                <div class="row-details-container">
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'orderDetails.orderNo' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ '#' + item.id }}</span>
                                        </div>
                                    </div>
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'orderDetails.address' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ item.address + ', ' + (item.postalCode ? (item.postalCode + ', ') : '') + item.cityName }}</span>
                                        </div>
                                    </div>
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'orderDetails.phoneNumber' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ item.phoneNumber }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-details-container">
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'orderDetails.priority' | translate}}</span>
                                        <div class="row-details-value">
                                            <app-tile [type]="getPriorityTileType(item.prioritySymbol)">
                                                {{ item.priorityName }}
                                            </app-tile>
                                        </div>
                                    </div>
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'orderDetails.status' | translate}}</span>
                                        <div class="row-details-value">
                                            <app-tile [type]="getStatusTileType(item.statusSymbol)">
                                                {{ item.statusName }}
                                            </app-tile>
                                        </div>
                                    </div>
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'orderDetails.isOverdue' | translate}}</span>
                                        <div class="row-details-value">
                                            <app-tile [type]="item.isOverdue ? 'error' : 'success'">
                                                {{ (item.isOverdue ? 'basic.yes' : 'basic.no') | translate}}
                                            </app-tile>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-details-container">
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'orderDetails.dateCreated' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ item.dateCreated | date:'dd-MM-yyyy' }}</span>
                                        </div>
                                    </div>
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'orderDetails.dateDeadline' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ item.dateDeadline | date:'dd-MM-yyyy' }}</span>
                                        </div>
                                    </div>
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'orderDetails.dateCompleted' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ (item.dateCompleted | date:'dd-MM-yyyy') ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-details-container">
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'orderDetails.remarks' | translate}}</span>
                                        <div class="row-details-value">
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

        <div class="w-full mt-9">
            <app-card [isContentCentered]="true" overflowType="visible">
                <app-pagination [totalItems]="ordersCount()" (change)="onOrderPaginationChange($event)"></app-pagination>
            </app-card>
        </div>

        <app-order-form-modal #orderFormModal (orderSaved)="loadOrders()" />
    `,
    styles: [``]
})
export class OrderListComponent implements OnInit {
    @ViewChild('orderFormModal') orderFormModal!: OrderFormModalComponent;

    private readonly orderService = inject(OrderService);
    private readonly promptModalService = inject(PromptModalService);
    private readonly translateService = inject(TranslateService);
    private readonly toastService = inject(ToastService);

    protected readonly filterType = FilterType;
    protected readonly status = Status;

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
            ? 'error'
            : 'primary';
    }

    protected getStatusTileType(type: Status): TileType {
        switch(type) {
            case Status.in_progress:
                return 'warning'
            case Status.completed:
                return 'success';
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
