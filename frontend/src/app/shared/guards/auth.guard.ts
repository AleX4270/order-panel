import { inject } from '@angular/core';
import { CanActivateFn } from '@angular/router';
import { Store } from '@ngxs/store';
import { AuthState } from '../store/auth/auth.state';
import { Router } from '@angular/router';

export const authGuard: CanActivateFn = (route, state) => {
    const store = inject(Store);
    const router = inject(Router);
    const isAuthenticated = store.selectSignal(AuthState.isAuthenticated);

    if(isAuthenticated()) {
        return true;
    }

    return router.createUrlTree(['/']);
};
