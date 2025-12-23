import { Component, inject, OnInit, signal, ViewChild, WritableSignal } from '@angular/core';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { ListTableComponent } from '../../shared/components/list-table/list-table.component';
import { ExpansionState, ToastType } from '../../shared/enums/enums';
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
import { Role } from '../../shared/enums/role.enum';
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
    DatePipe,
    NgClass,
    UserFormModalComponent,
    ButtonComponent
],
    providers: [provideIcons({faEye, faPenToSquare, faTrashCan})],
    template: `
        <div class="w-full user-list-header">
            <h1 class="font-semibold text-2xl mb-5">{{'userList.header' | translate}}</h1>
            <app-card overflowType="visible" [title]="'basic.filters' | translate">
                <app-filters 
                    [type]="filterType.userListFilters"
                    (filtersChange)="onUserFiltersChange($event)"
                />        
            </app-card>
        </div>

        <div class="w-full mt-5 flex justify-between items-end">
            <div class="flex flex-col">
                <h2 class="font-medium text-xl">{{ ('userList.users' | translate) + ' (' + usersCount() + ')'}}</h2>
            </div>
            <app-button classList="btn btn-sm btn-primary md:btn-md" (click)="showUserFormModal()">{{'userList.addNewUser' | translate}}</app-button>
        </div>

        <div class="w-full mt-2">
            <app-list-table
                [defineTableRowsExternally]="true"
                [data]="users()"
            >
                <ng-template #headers>
                    <th class="cursor-pointer" (click)="onSortChange('userNumber')">{{'userListTable.userNumber' | translate}}</th>
                    <th class="cursor-pointer" (click)="onSortChange('name')">{{'userListTable.name' | translate}}</th>
                    <th class="cursor-pointer" (click)="onSortChange('email')">{{'userListTable.email' | translate}}</th>
                    <th class="cursor-pointer" (click)="onSortChange('roles')">{{'userListTable.roles' | translate}}</th>
                    <th class="cursor-pointer" (click)="onSortChange('dateCreated')">{{'userListTable.dateCreated' | translate}}</th>
                    <th class="cursor-pointer" (click)="onSortChange('dateUpdated')">{{'userListTable.dateUpdated' | translate}}</th>
                    <th>{{'userListTable.actions' | translate}}</th>
                </ng-template>

                <ng-template #rows let-item>
                    <tr class="bg-base-100 hover:bg-base-200 [&_td]:text-xs p-1">
                        <td class="font-normal"><span>{{ '#' + item.id }}</span></td>
                        <td>
                            <div class="flex flex-col">
                                <span>{{ item.name }}</span>
                                @if(item.firstName || item.lastName) {
                                    <span class="text-base-content/50">{{ (item.firstName ?? '') + (item.lastName ? (' ' + item.lastName) : '') }}</span>
                                }
                            </div>
                        </td>
                        <td><span>{{ item.email }}</span></td>
                        <td>
                            <div class="flex gap-1">
                                @for(role of item.roles; track role.id) {
                                    <app-tile [type]="getRoleTileType(role.symbol)">
                                        {{ role.name }}
                                    </app-tile>
                                }
                                @empty {
                                    <span>-</span>
                                }
                            </div>
                        </td>
                        <td><span>{{ item.dateCreated | date:'dd-MM-yyyy' }}</span></td>
                        <td><span>{{ item.dateUpdated | date:'dd-MM-yyyy'}}</span></td>
                        <td>
                            <div class="flex gap-3">
                                <ng-icon
                                    class="item-pressable text-dark"
                                    name="faEye"
                                    size="18px"
                                    (click)="toggleItemDetailsExpansion(item.id)"
                                ></ng-icon>

                                <ng-icon
                                    class="item-pressable text-primary"
                                    name="faPenToSquare"
                                    size="18px"
                                    (click)="showUserFormModal(item.id)"
                                ></ng-icon>

                                @if(!item.isInternal) {
                                    <ng-icon
                                        class="item-pressable text-danger"
                                        name="faTrashCan"
                                        size="18px"
                                        (click)="showUserDeletePromptModal(item.id)"
                                    ></ng-icon>
                                }
                            </div>
                        </td>
                    </tr>

                    <tr [class.hidden]="!hasVisibleDetails(item.id)">
                        <td colspan="7" class="p-0">
                            <div class="p-3">
                                <div class="w-full">
                                    <h6 class="text-primary text-sm">{{ 'userDetails.header' | translate}}</h6>
                                </div>

                                <div class="row-details-container">
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'userDetails.userNumber' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ '#' + item.id }}</span>
                                        </div>
                                    </div>
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'userDetails.firstAndLastName' | translate}}</span>
                                        <div class="row-details-value">
                                            <span class="address-label">{{ (item.firstName ?? '') + (item.lastName ? (' ' + item.lastName) : '-') }}</span>
                                        </div>
                                    </div>
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'userDetails.name' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ item.name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-details-container">
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'userDetails.email' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ item.email  }}</span>
                                        </div>
                                    </div>
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'userDetails.dateCreated' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ item.dateCreated | date:'dd-MM-yyyy' }}</span>
                                        </div>
                                    </div>
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'userDetails.dateUpdated' | translate}}</span>
                                        <div class="row-details-value">
                                            <span>{{ item.dateUpdated | date:'dd-MM-yyyy' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row-details-container">
                                    <div class="row-details-box">
                                        <span class="row-details-label">{{ 'userDetails.roles' | translate}}</span>
                                        <div class="row-details-value flex gap-1">
                                            @for(role of item.roles; track role.id) {
                                                <app-tile [type]="getRoleTileType(role.symbol)">
                                                    {{ role.name }}
                                                </app-tile>
                                            }
                                            @empty {
                                                <span>-</span>
                                            }
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </ng-template>
            </app-list-table>
        </div>

        <div class="w-full mt-8">
            <app-card [isContentCentered]="true" overflowType="visible">
                <app-pagination [totalItems]="usersCount()" (change)="onUsersPaginationChange($event)"></app-pagination>
            </app-card>
        </div>

        <app-user-form-modal #userFormModal (userSaved)="loadUsers()" />
    `,
    styles: [``]
})
export class UserListComponent implements OnInit {
    @ViewChild('userFormModal') userFormModal!: UserFormModalComponent;

    private readonly userService = inject(UserService);
    private readonly promptModalService = inject(PromptModalService);
    private readonly translateService = inject(TranslateService);
    private readonly toastService = inject(ToastService);

    protected readonly filterType = FilterType;

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

    protected getRoleTileType(roleSymbol: Role): TileType {
        switch(roleSymbol) {
            case Role.admin:
                return 'error';
            case Role.manager:
                return 'warning';
            case Role.worker:
                return 'primary';
        }
    }
}
