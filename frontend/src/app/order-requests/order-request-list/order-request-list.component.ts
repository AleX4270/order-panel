import { Component, inject, OnInit, signal, WritableSignal } from '@angular/core';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { ListTableComponent } from '../../shared/components/list-table/list-table.component';
import { ExpansionState, ToastType } from '../../shared/enums/enums';
import { NgIcon, provideIcons } from '@ng-icons/core';
import { faEye, faPenToSquare, faTrashCan, faCircleCheck, faCircleXmark } from '@ng-icons/font-awesome/regular';
import { CardComponent } from '../../shared/components/card/card.component';
import { PaginationComponent } from '../../shared/components/pagination/pagination.component';
import { FiltersComponent } from '../../shared/components/filters/filters.component';
import { FilterType } from '../../shared/enums/filter-type.enum';
import { DatePipe } from '@angular/common';
import { PaginationItem } from '../../shared/types/pagination.types';
import { SortItem } from '../../shared/types/sort.types';
import { PromptModalService } from '../../shared/services/prompt-modal/prompt-modal.service';
import { ToastService } from '../../shared/services/toast/toast.service';
import { HasPermissionDirective } from '../../shared/directives/has-permission.directive';
import { Permission } from '../../shared/enums/permission.enum';
import { CompanyService } from '../../shared/services/api/company/company.service';
import { CompanyItem } from '../../shared/types/company.types';
import { DEFAULT_COORDINATES } from '../../shared/constants/map.const';
import { Coordinates } from '../../shared/types/address.types';
import { OrderRequestFilterParams, OrderRequestItem } from '../../shared/types/order-request.types';
import { OrderRequestService } from '../../shared/services/api/order-request/order-request.service';
import { OrderRequestMapComponent } from '../../shared/components/order-request-map/order-request-map.component';
import { CastToTelHrefPipe } from '../../shared/pipes/cast-to-tel-href.pipe';
import { TruncatePipe } from '../../shared/pipes/truncate.pipe';

@Component({
    selector: 'app-order-request-list',
    imports: [
    TranslatePipe,
    ListTableComponent,
    NgIcon,
    CardComponent,
    PaginationComponent,
    FiltersComponent,
    DatePipe,
    HasPermissionDirective,
    OrderRequestMapComponent,
    CastToTelHrefPipe,
    TruncatePipe,
],
    providers: [provideIcons({faEye, faPenToSquare, faTrashCan, faCircleCheck, faCircleXmark})],
    template: `
        <div class="w-full order-list-header">
            <h1 class="font-semibold text-2xl mb-5">{{'orderRequestList.header' | translate}}</h1>
            <app-card overflowType="visible" [title]="'basic.filters' | translate" [isCollapsible]="true">
                <app-filters
                    [type]="filterType.orderRequestListFilters"
                    (filtersChange)="onFiltersChange($event)"
                />
            </app-card>
        </div>

        <div class="flex flex-col min-h-1/2 lg:flex-row gap-3">
            <div class="mt-5 h-180 lg:w-2/5">
                <app-order-request-map [orderRequests]="orderRequests()" [coordinates]="companyCoordinates()"></app-order-request-map>
            </div>

            <div class="mt-6 lg:w-3/5">
                <div class="w-full flex justify-between items-end">
                    <div class="flex flex-col">
                        <h2 class="font-medium text-xl">{{ ('orderRequestList.orderRequests' | translate) + ' (' + orderRequestsCount() + ')'}}</h2>
                    </div>
                </div>

                <div class="w-full mt-2">
                    <app-list-table
                        [defineTableRowsExternally]="true"
                        [data]="orderRequests()"
                    >
                        <ng-template #headers>
                            <th class="cursor-pointer" (click)="onOrderRequestSortChange('orderRequestNumber')">{{'orderRequestListTable.orderRequestNo' | translate}}</th>
                            <th class="cursor-pointer">{{'orderRequestListTable.address' | translate}}</th>
                            <th class="cursor-pointer">{{'orderRequestListTable.client' | translate}}</th>
                            <th class="cursor-pointer" (click)="onOrderRequestSortChange('dateCreated')">{{'orderRequestListTable.dateCreated' | translate}}</th>
                            <th class="cursor-pointer" (click)="onOrderRequestSortChange('remarks')">{{'orderRequestListTable.remarks' | translate}}</th>
                            <th>{{'orderRequestListTable.actions' | translate}}</th>
                        </ng-template>

                        <ng-template #rows let-item>
                            <tr
                                class="bg-base-100 [&_td]:text-xs p-1"
                            >
                                <td class="font-normal"><span>{{ '#' + item.id }}</span></td>
                                <td>
                                    <div class="flex flex-col">
                                        <span class="text-xs">{{item.cityName}}</span>
                                        <span class="text-base-content/70 font-light mt-1">{{item.address}}</span>
                                        <span class="text-base-content/50 font-light mt-1">{{item.distance}} km</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-col">
                                        <span class="text-xs">{{item.firstName + ' ' + item.lastName}}</span>
                                        <a class="text-primary/70 hover:underline mt-1" [href]="item.phoneNumber | castToTelHref">{{item.phoneNumber}}</a>
                                        <a class="text-primary/50 hover:underline mt-1" [href]="'mailto:' + item.email">{{item.email}}</a>
                                    </div>
                                </td>
                                <td><span>{{ item.dateCreated | date:'dd-MM-yyyy' }}</span></td>
                                <td class="text-base-content/80 font-light">{{ (item.remarks ?? '-') | truncate:40}}</td>
                                <td>
                                    <div class="flex gap-3">
                                        <ng-icon
                                            *hasPermission="permission.order_requests_show"
                                            class="item-pressable"
                                            name="faEye"
                                            size="19px"
                                            (click)="toggleItemDetailsExpansion(item.id)"
                                        ></ng-icon>

                                        <ng-icon
                                            *hasPermission="permission.order_requests_manage"
                                            class="item-pressable [&>svg]:fill-success"
                                            name="faCircleCheck"
                                            size="19px"
                                            (click)="showAcceptPromptModal(item.id)"
                                        ></ng-icon>

                                        <ng-icon
                                            *hasPermission="permission.order_requests_manage"
                                            class="item-pressable [&>svg]:fill-error"
                                            name="faCircleXmark"
                                            size="19px"
                                            (click)="showRejectPromptModal(item.id)"
                                        ></ng-icon>
                                    </div>
                                </td>
                            </tr>

                            <tr [class.hidden]="!hasVisibleDetails(item.id)" class="hover:!bg-base-100">
                                <td colspan="7" class="p-0">
                                    <div class="p-3">
                                        <div class="w-full">
                                            <h6 class="text-primary text-sm">{{ 'orderRequestDetails.header' | translate}}</h6>
                                        </div>

                                        <div class="row-details-container">
                                            <div class="row-details-box">
                                                <span class="row-details-label">{{ 'orderRequestDetails.orderRequestNo' | translate}}</span>
                                                <div class="row-details-value">
                                                    <span>{{ '#' + item.id }}</span>
                                                </div>
                                            </div>
                                            <div class="row-details-box">
                                                <span class="row-details-label">{{ 'orderRequestDetails.address' | translate}}</span>
                                                <div class="row-details-value">
                                                    <span>{{ item.address + ', ' + (item.postalCode ? (item.postalCode + ', ') : '') + item.cityName }}</span>
                                                </div>
                                            </div>
                                            <div class="row-details-box">
                                                <span class="row-details-label">{{ 'orderRequestDetails.client' | translate}}</span>
                                                <div class="row-details-value">
                                                    <span>{{item.firstName + ' ' + item.lastName}}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row-details-container">
                                            <div class="row-details-box">
                                                <span class="row-details-label">{{ 'orderRequestDetails.phoneNumber' | translate}}</span>
                                                <div class="row-details-value">
                                                    <a class="text-primary/85 hover:underline" [href]="item.phoneNumber | castToTelHref">{{item.phoneNumber}}</a>
                                                </div>
                                            </div>
                                            <div class="row-details-box">
                                                <span class="row-details-label">{{ 'orderRequestDetails.email' | translate}}</span>
                                                <div class="row-details-value">
                                                    <a class="text-primary/85 hover:underline" [href]="'mailto:' + item.email">{{item.email}}</a>
                                                </div>
                                            </div>
                                            <div class="row-details-box">
                                                <span class="row-details-label">{{ 'orderRequestDetails.dateCreated' | translate}}</span>
                                                <div class="row-details-value">
                                                    <span>{{ item.dateCreated | date:'dd-MM-yyyy' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row-details-container max-w-5xl">
                                            <div class="row-details-box">
                                                <span class="row-details-label">{{ 'orderRequestDetails.remarks' | translate}}</span>
                                                <div class="row-details-value">
                                                    <span class="text-muted break-all">{{ item.remarks ?? '-' }}</span>
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
        </div>

        <div class="w-full mt-9 mb-6">
            <app-card [isContentCentered]="true" overflowType="visible">
                <app-pagination [totalItems]="orderRequestsCount()" (change)="onPaginationChange($event)"></app-pagination>
            </app-card>
        </div>
    `,
    styles: [``]
})
export class OrderRequestListComponent implements OnInit {  
    private readonly orderRequestService = inject(OrderRequestService);
    private readonly promptModalService = inject(PromptModalService);
    private readonly translateService = inject(TranslateService);
    private readonly toastService = inject(ToastService);
    private readonly companyService = inject(CompanyService);

    protected readonly filterType = FilterType;
    protected readonly permission = Permission;

    protected expansionState = ExpansionState;
    protected itemDetailsExpansionState: Partial<Record<number, ExpansionState>> = {};

    protected orderRequests: WritableSignal<OrderRequestItem[]> = signal<OrderRequestItem[]>([]);
    protected orderRequestsCount: WritableSignal<number> = signal<number>(0);
    protected companyCoordinates = signal<Coordinates>(DEFAULT_COORDINATES);

    protected filterValues: Partial<Record<string, string | number[] | null>> = {};
    protected paginationValues: PaginationItem | null = null;
    protected sortValues: WritableSignal<SortItem | null> = signal<SortItem | null>(null);

    ngOnInit(): void {
        this.loadOrderRequests();
        this.loadCompanyDetails();
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

    protected showAcceptPromptModal(id: number): void {
        this.promptModalService.openModal({
            title: this.translateService.instant('orderRequestAcceptPromptModal.title'),
            message: this.translateService.instant('orderRequestAcceptPromptModal.message'),
            handler: () => {
                this.acceptOrderRequest(id);
            }
        });
    }

    protected showRejectPromptModal(id: number): void {
        this.promptModalService.openModal({
            title: this.translateService.instant('orderRequestRejectPromptModal.title'),
            message: this.translateService.instant('orderRequestRejectPromptModal.message'),
            handler: () => {
                this.rejectOrderRequest(id);
            }
        });
    }

    protected acceptOrderRequest(id: number): void {
        this.orderRequestService.castToOrder(id).subscribe({
            next: () => {
                this.toastService.show(
                    this.translateService.instant('orderRequestList.acceptSuccess'),
                    ToastType.success,
                );
                this.loadOrderRequests();
            },
            error: (err) => {
                console.error(err);
                this.toastService.show(
                    this.translateService.instant('orderRequestList.acceptError'),
                    ToastType.danger,
                );
            }
        })
    }

    protected rejectOrderRequest(id: number): void {
        this.orderRequestService.delete(id).subscribe({
            next: () => {
                this.toastService.show(
                    this.translateService.instant('orderRequestList.rejectSuccess'),
                    ToastType.success,
                );
                this.loadOrderRequests();
            },
            error: (err) => {
                console.error(err);
                this.toastService.show(
                    this.translateService.instant('orderRequestList.rejectError'),
                    ToastType.danger,
                );
            }
        })
    }

    protected loadOrderRequests(): void {
        let params = {} as OrderRequestFilterParams;

        if(Object.keys(this.filterValues).length > 0) {
            Object.keys(this.filterValues).forEach((key) => {
                params = {
                    ...params,
                    [key]: this.filterValues[key],
                }
            })
        }

        if(this.paginationValues?.page) {
            params.page = this.paginationValues.page;
        }

        if(this.paginationValues?.pageSize) {
            params.pageSize = this.paginationValues.pageSize;
        }

        if(this.sortValues()?.sortColumn) {
            params.sortColumn = this.sortValues()?.sortColumn;
        }

        if(this.sortValues()?.sortDir) {
            params.sortDir = this.sortValues()?.sortDir;
        }

        this.orderRequestService.index(params).subscribe({
            next: (res) => {
                this.orderRequests.set(res.data?.items ?? []);
                this.orderRequestsCount.set(res.data?.count ?? 0);
            },
            error: (err) => {
                console.error(err);
            },
        });
    }

    private loadCompanyDetails(): void {
        this.companyService.show().subscribe({
            next: (res) => {
                const company: CompanyItem | null = res.data;

                if(!company) {
                    this.toastService.show(this.translateService.instant('orderRequestList.companyDetailsLoadError'), ToastType.danger);
                    return;
                }

                this.companyCoordinates.set(company.coordinates);
            },
            error: (err) => {
                console.error(err);
                this.toastService.show(this.translateService.instant('orderRequestList.companyDetailsLoadError'), ToastType.danger);
            }
        })
    }

    protected onFiltersChange(filterValues: Partial<Record<string, string | number[] | null>>): void {
        this.filterValues = filterValues;
        this.loadOrderRequests();
    }

    protected onPaginationChange(paginationValues: PaginationItem): void {
        this.paginationValues = paginationValues;
        this.loadOrderRequests();
    }

    protected onOrderRequestSortChange(column: string): void {
        if(this.sortValues()?.sortColumn !== column) {
            this.sortValues.set({
                sortColumn: column,
                sortDir: 'desc',
            });
        }

        if(this.sortValues()?.sortDir === 'asc') {
            this.sortValues.set({
                sortColumn: column,
                sortDir: 'desc',
            });
        }
        else {
            this.sortValues.set({
                sortColumn: column,
                sortDir: 'asc',
            });
        }

        this.loadOrderRequests();
    }
}
