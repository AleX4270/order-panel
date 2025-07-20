import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { Store } from '@ngxs/store';
import { AuthState } from '../store/auth/auth.state';

export const guestGuard: CanActivateFn = (route, state) => {
    const store = inject(Store);
    const router = inject(Router);
    const isAuthenticated = store.selectSignal(AuthState.isAuthenticated);

    if(isAuthenticated()) {
        return router.createUrlTree(['/dashboard']);
    }

    return true;
};
