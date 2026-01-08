import { inject, Injectable } from '@angular/core';
import { Store } from '@ngxs/store';
import { UserState } from '../../store/user/user.state';
import { Permission } from '../../enums/permission.enum';

@Injectable({
  providedIn: 'root',
})
export class UserPermissionService {
    private readonly store = inject(Store);
    private readonly userPermissions = this.store.selectSignal(UserState.permissions);

    public hasAnyPermissions(permissions: string | string[] | Permission | Permission[]): boolean {
        const requiredPermissions = Array.isArray(permissions) ? permissions : [permissions];
        return requiredPermissions.some(p => this.userPermissions().includes(p));
    }

    public hasEveryPermission(permissions: string | string[] | Permission | Permission[]): boolean {
        const requiredPermissions = Array.isArray(permissions) ? permissions : [permissions];
        return requiredPermissions.every(p => this.userPermissions().includes(p));
    }
}
