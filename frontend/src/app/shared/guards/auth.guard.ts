import { inject } from '@angular/core';
import { CanActivateFn } from '@angular/router';
import { Store } from '@ngxs/store';
import { UserState } from '../store/user/user.state';
import { Router } from '@angular/router';

export const authGuard: CanActivateFn = (route, state) => {
    const store = inject(Store);
    const router = inject(Router);
    const isAuthenticated = store.selectSignal(UserState.isAuthenticated);

    if(isAuthenticated()) {
        return true;
    }

    return router.createUrlTree(['/']);
};
