import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { Store } from '@ngxs/store';
import { UserState } from '../store/user/user.state';

export const guestGuard: CanActivateFn = (route, state) => {
    const store = inject(Store);
    const router = inject(Router);
    const isAuthenticated = store.selectSignal(UserState.isAuthenticated);

    if(isAuthenticated()) {
        return router.createUrlTree(['/dashboard']);
    }

    return true;
};
