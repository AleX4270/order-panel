import { Component, OnInit, signal, TemplateRef, ViewChild, WritableSignal } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';
import { ListTableComponent } from '../../shared/components/list-table/list-table.component';
import { ExpansionState, TileType, PriorityType } from '../../shared/enums/enums';
import { TileComponent } from "../../shared/components/tile/tile.component";
import { NgIcon, provideIcons } from '@ng-icons/core';
import { faEye, faPenToSquare, faTrashCan } from '@ng-icons/font-awesome/regular';
import { ColorLabelComponent } from '../../shared/components/color-label/color-label.component';
import { CardComponent } from "../../shared/components/card/card.component";
import { PaginationComponent } from '../../shared/components/pagination/pagination.component';
import { FiltersComponent } from '../../shared/components/filters/filters.component';
import { FilterType } from '../../shared/enums/filter-type.enum';
import { OrderFormModalComponent } from "../order-form-modal/order-form-modal.component";

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
    OrderFormModalComponent
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
                                (filtersChange)="null"
                            />        
                        </app-card>
                    </div>
                </div>
            </div> 
        </div>

        <div class="row order-list-info mt-5">
            <div class="col-8">
                <h5>Zlecenia: W trakcie (5)</h5>
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
                <button class="btn btn-primary" (click)="showOrderFormModal()" >+ {{'orderList.addNewOrder' | translate}}</button>
            </div>
        </div>

        <div class="row order-list-table mt-2">
            <div class="col-12">
                <app-list-table
                    [defineTableRowsExternally]="true"
                    [data]="[
                        {
                            id: 1,
                            number: '1/2025',
                            address: 'Wiry 22/24',
                            priority: {
                                id: 1,
                                symbol: 'normal',
                                label: 'Normalny',
                            },
                            dateCreation: '2024-01-01',
                            dateDeadline: '2024-01-15',
                            remarks: 'Proszę o kontakt przed przyjazdem',
                        },
                        {
                            id: 2,
                            number: '2/2025',
                            address: 'Nałęczów 22/24',
                            priority: {
                                id: 1,
                                symbol: 'high',
                                label: 'Wysoki',
                            },
                            dateCreation: '2024-01-05',
                            dateDeadline: '2024-01-20',
                            remarks: 'Proszę o nie dzwonienie',
                        },
                        {
                            id: 3,
                            number: '2/2025',
                            address: 'Nałęczów 22/24',
                            priority: {
                                id: 1,
                                symbol: 'high',
                                label: 'Wysoki',
                            },
                            dateCreation: '2024-01-05',
                            dateDeadline: '2024-01-20',
                            remarks: 'Proszę o nie dzwonienie',
                        }
                    ]"
                >
                    <ng-template #headers>
                        <th>{{'orderListTable.orderNo' | translate}}</th>
                        <th>{{'orderListTable.address' | translate}}</th>
                        <th>{{'orderListTable.priority' | translate}}</th>
                        <th>{{'orderListTable.dateCreated' | translate}}</th>
                        <th>{{'orderListTable.dateDeadline' | translate}}</th>
                        <th>{{'orderListTable.remarks' | translate}}</th>
                        <th>{{'orderListTable.actions' | translate}}</th>
                    </ng-template>

                    <ng-template #rows let-item>
                        <tr class="list-row">
                            <td class="fw-semibold">{{ item.number }}</td>
                            <td >{{ item.address }}</td>
                            <td>
                                <app-tile [type]="getPriorityTileType(item.priority.symbol)">
                                    {{ item.priority.label }}
                                </app-tile>
                            </td>
                            <td>{{ item.dateCreation }}</td>
                            <td>{{ item.dateDeadline }}</td>
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
                                    ></ng-icon>

                                    <ng-icon
                                        class="item-pressable text-danger"
                                        name="faTrashCan"
                                        size="20px"
                                    ></ng-icon>
                                </div>
                            </td>
                        </tr>

                        <tr [class.d-none]="!hasVisibleDetails(item.id)">
                            <td colspan="7" class="p-0">
                                <div class="expandable-content">
                                    Szczegóły zlecenia
                                </div>
                            </td>
                        </tr>
                    </ng-template>
                </app-list-table>
            </div>
        </div>

        <div class="row order-list-pagination mt-5">
            <div class="col-12">
                <app-card [isContentCentered]="true" overflowType="visible">
                    <app-pagination></app-pagination>
                </app-card>
            </div>
        </div>

        <app-order-form-modal #orderFormModal />
    `,
    styles: [`
        .list-row {
            background-color: var(--order-list-default-row-background-color);
            border-bottom: 1px solid var(--order-list-row-border-color);

            &:hover {
                background-color: var(--order-list-hovered-row-background-color);
            }

            td {
                font-size: var(--font-size-sm);
                padding: 1.5rem 0 1.5rem 0.75rem;
            }
        }

        .overdue-row {
            background-color: var(--order-list-overdue-row-background-color);
        }

        .completed-row {
            background-color: var(--order-list-completed-row-background-color);
        }
    `]
})
export class OrderListComponent {
    @ViewChild('orderFormModal') orderFormModal!: OrderFormModalComponent;

    protected readonly filterType = FilterType;
    protected expansionState = ExpansionState;
    protected itemDetailsExpansionState: Partial<Record<number, ExpansionState>> = {};

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

    protected getPriorityTileType(type: PriorityType): TileType {
        return type === PriorityType.high
            ? TileType.danger
            : TileType.secondary;
    }

    protected showOrderFormModal(id?: number): void {
        this.orderFormModal.showForm(id);
    }
}
