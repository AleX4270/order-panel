import { inject } from '@angular/core';
import { CanActivateFn } from '@angular/router';
import { Store } from '@ngxs/store';
import { UserState } from '../store/user/user.state';
import { Router } from '@angular/router';
import { UserPermissionService } from '../services/user-permission/user-permission.service';
import { Permission } from '../enums/permission.enum';

export const authGuard = (requiredPermissions?: string | string[] | Permission | Permission[], requireEveryPermission: boolean = true): CanActivateFn => {
    return () => {
        const store = inject(Store);
        const router = inject(Router);
        const userPermissionService = inject(UserPermissionService);

        const isAuthenticated = store.selectSignal(UserState.isAuthenticated);
        if(!isAuthenticated()) {
            router.createUrlTree(['/']);
            return false;
        }

        if(!requiredPermissions) {
            return true;
        }

        const hasPermissions = requireEveryPermission
            ? userPermissionService.hasEveryPermission(requiredPermissions)
            : userPermissionService.hasAnyPermissions(requiredPermissions);

        if(!hasPermissions) {
            return false;
        }

        return true;
    };
}
