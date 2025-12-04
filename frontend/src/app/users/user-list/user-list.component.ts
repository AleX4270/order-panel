import { Component, inject, OnInit, signal, ViewChild, WritableSignal } from '@angular/core';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { ListTableComponent } from '../../shared/components/list-table/list-table.component';
import { ExpansionState, TileType, ToastType } from '../../shared/enums/enums';
import { TileComponent } from "../../shared/components/tile/tile.component";
import { NgIcon, provideIcons } from '@ng-icons/core';
import { faEye, faPenToSquare, faTrashCan } from '@ng-icons/font-awesome/regular';
import { ColorLabelComponent } from '../../shared/components/color-label/color-label.component';
import { CardComponent } from "../../shared/components/card/card.component";
import { PaginationComponent } from '../../shared/components/pagination/pagination.component';
import { FiltersComponent } from '../../shared/components/filters/filters.component';
import { FilterType } from '../../shared/enums/filter-type.enum';
import { DatePipe, NgClass } from '@angular/common';
import { PaginationItem } from '../../shared/types/pagination.types';
import { SortItem } from '../../shared/types/sort.types';
import { PromptModalService } from '../../shared/services/prompt-modal/prompt-modal.service';
import { ToastService } from '../../shared/services/toast/toast.service';
import { UserService } from '../../shared/services/api/user/user.service';
import { UserFilterParams, UserItem } from '../../shared/types/user.types';
import { UserFormModalComponent } from "../user-form-modal/user-form-modal.component";

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
    DatePipe,
    NgClass,
    UserFormModalComponent
],
    providers: [provideIcons({faEye, faPenToSquare, faTrashCan})],
    template: `
        <div class="row order-list-header">
            <div class="col-12">
                <div class="row">
                    <div class="col-6">
                        <h3>{{'userList.header' | translate}}</h3>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <app-card overflowType="visible">
                            <app-filters 
                                [type]="filterType.userListFilters"
                                (filtersChange)="onUserFiltersChange($event)"
                            />        
                        </app-card>
                    </div>
                </div>
            </div> 
        </div>

        <div class="row order-list-info mt-5">
            <div class="col-8">
                <h5>{{ ('userList.users' | translate) + ' (' + usersCount() + ')'}}</h5>
            </div>
            <div class="col-4 text-end">
                <button class="btn btn-sm btn-primary" (click)="showUserFormModal()" >+ {{'userList.addNewUser' | translate}}</button>
            </div>
        </div>

        <div class="row order-list-table mt-2">
            <div class="col-12">
                <app-list-table
                    [defineTableRowsExternally]="true"
                    [data]="users()"
                >
                    <ng-template #headers>
                        <th class="cursor-pointer" (click)="onSortChange('userNumber')">{{'userListTable.userNumber' | translate}}</th>
                        <th class="cursor-pointer" (click)="onSortChange('name')">{{'userListTable.name' | translate}}</th>
                        <th class="cursor-pointer" (click)="onSortChange('email')">{{'userListTable.email' | translate}}</th>
                        <th class="cursor-pointer" (click)="onSortChange('dateCreated')">{{'userListTable.dateCreated' | translate}}</th>
                        <th class="cursor-pointer" (click)="onSortChange('dateUpdated')">{{'userListTable.dateUpdated' | translate}}</th>
                        <th>{{'userListTable.actions' | translate}}</th>
                    </ng-template>

                    <ng-template #rows let-item>
                        <tr class="list-row">
                            <td class="fw-semibold">{{ '#' + item.id }}</td>
                            <td>
                                <div class="user-name-container d-flex flex-column">
                                    <span class="user-name">{{ item.name }}</span>
                                    @if(item.firstName || item.lastName) {
                                        <span class="user-name-label text-muted">{{ (item.firstName ?? '') + (item.lastName ? (' ' + item.lastName) : '') }}</span>
                                    }
                                </div>
                            </td>
                            <td>{{ item.email }}</td>
                            <td>{{ item.dateCreated | date:'dd-MM-yyyy' }}</td>
                            <td>{{ item.dateUpdated | date:'dd-MM-yyyy'}}</td>
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
                                        (click)="showUserFormModal(item.id)"
                                    ></ng-icon>

                                    <ng-icon
                                        class="item-pressable text-danger"
                                        name="faTrashCan"
                                        size="20px"
                                        (click)="showUserDeletePromptModal(item.id)"
                                    ></ng-icon>
                                </div>
                            </td>
                        </tr>

                        <tr [class.d-none]="!hasVisibleDetails(item.id)">
                            <td colspan="7" class="p-0">
                                <div class="expandable-content details p-3">
                                    <div class="row">
                                        <div class="col">
                                            <h6 class="details-header text-primary">{{ 'userDetails.header' | translate}}</h6>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'userDetails.userNumber' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ '#' + item.id }}</span>
                                            </div>
                                        </div>
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'userDetails.firstAndLastName' | translate}}</span>
                                            <div class="details-value">
                                                <span class="address-label">{{ (item.firstName ?? '') + (item.lastName ? (' ' + item.lastName) : '') }}</span>
                                            </div>
                                        </div>
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'userDetails.name' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ item.name }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'userDetails.email' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ item.email  }}</span>
                                            </div>
                                        </div>
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'userDetails.dateCreated' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ item.dateCreated | date:'dd-MM-yyyy' }}</span>
                                            </div>
                                        </div>
                                        <div class="col d-flex flex-column">
                                            <span class="details-label text-muted">{{ 'userDetails.dateUpdated' | translate}}</span>
                                            <div class="details-value">
                                                <span>{{ item.dateUpdated | date:'dd-MM-yyyy' }}</span>
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

        <div class="row user-list-pagination mt-5 pb-4">
            <div class="col-12">
                <app-card [isContentCentered]="true" overflowType="visible">
                    <app-pagination [totalItems]="usersCount()" (change)="onUsersPaginationChange($event)"></app-pagination>
                </app-card>
            </div>
        </div>

        <app-user-form-modal #userFormModal (userSaved)="loadUsers()" />
    `,
    styles: [`
        .details {
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
                padding: 0.9rem 0 0.9rem 0.75rem;
            }
        }

        .user-name-container {
            .user-name-label {
                font-size: var(--font-size-xs);
                font-weight: var(--font-weight-light);
            }
        }
    `]
})
export class UserListComponent implements OnInit {
    @ViewChild('userFormModal') userFormModal!: UserFormModalComponent;

    private readonly userService = inject(UserService);
    private readonly promptModalService = inject(PromptModalService);
    private readonly translateService = inject(TranslateService);
    private readonly toastService = inject(ToastService);

    protected readonly filterType = FilterType;
    protected readonly tileType = TileType;

    protected expansionState = ExpansionState;
    protected itemDetailsExpansionState: Partial<Record<number, ExpansionState>> = {};

    protected users: WritableSignal<UserItem[]> = signal<UserItem[]>([]);
    protected usersCount: WritableSignal<number> = signal<number>(0);

    protected userFilterValues: Partial<Record<string, string | number[] | null>> = {};
    protected userPaginationValues: PaginationItem | null = null;
    protected userSortValues: WritableSignal<SortItem | null> = signal<SortItem | null>(null);

    ngOnInit(): void {
        this.loadUsers();
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

    protected showUserFormModal(id?: number): void {
        this.userFormModal.showForm(id);
    }

    protected showUserDeletePromptModal(id: number): void {
        this.promptModalService.openModal({
            title: this.translateService.instant('userDeletePromptModal.title'),
            message: this.translateService.instant('userDeletePromptModal.message'),
            handler: () => {
                this.deleteUser(id);
            }
        });
    }

    protected deleteUser(id: number): void {
        this.userService.delete(id).subscribe({
            next: () => {
                this.toastService.show(
                    this.translateService.instant('userList.deleteSuccess'),
                    ToastType.success,
                );
                this.loadUsers();
            },
            error: (err) => {
                console.error(err);
                this.toastService.show(
                    this.translateService.instant('userList.deleteError'),
                    ToastType.danger,
                );
            }
        })
    }

    protected loadUsers(): void {
        let params = {} as UserFilterParams;

        if(Object.keys(this.userFilterValues).length > 0) {
            Object.keys(this.userFilterValues).forEach((key) => {
                params = {
                    ...params,
                    [key]: this.userFilterValues[key],
                }
            })
        }

        if(this.userPaginationValues?.page) {
            params.page = this.userPaginationValues.page;
        }

        if(this.userPaginationValues?.pageSize) {
            params.pageSize = this.userPaginationValues.pageSize;
        }

        if(this.userSortValues()?.sortColumn) {
            params.sortColumn = this.userSortValues()?.sortColumn;
        }

        if(this.userSortValues()?.sortDir) {
            params.sortDir = this.userSortValues()?.sortDir;
        }

        this.userService.index(params).subscribe({
            next: (res) => {
                this.users.set(res.data?.items ?? []);
                this.usersCount.set(res.data?.count ?? 0);
            },
            error: (err) => {
                console.error(err);
            },
        });
    }

    protected onUserFiltersChange(filterValues: Partial<Record<string, string | number[] | null>>): void {
        this.userFilterValues = filterValues;
        this.loadUsers();
    }

    protected onUsersPaginationChange(paginationValues: PaginationItem): void {
        this.userPaginationValues = paginationValues;
        this.loadUsers();
    }

    protected onSortChange(column: string): void {
        if(this.userSortValues()?.sortColumn !== column) {
            this.userSortValues.set({
                sortColumn: column,
                sortDir: 'desc',
            });
        }

        if(this.userSortValues()?.sortDir === 'asc') {
            this.userSortValues.set({
                sortColumn: column,
                sortDir: 'desc',
            });
        }
        else {
            this.userSortValues.set({
                sortColumn: column,
                sortDir: 'asc',
            });
        }

        this.loadUsers();
    }
}
