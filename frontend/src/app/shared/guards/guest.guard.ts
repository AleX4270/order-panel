import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { Store } from '@ngxs/store';
import { AuthState } from '../store/auth/auth.state';
import { map, tap } from 'rxjs';

export const guestGuard: CanActivateFn = (route, state) => {
    const store = inject(Store);
    const router = inject(Router);

    return store.select(AuthState.isAuthenticated).pipe(
        map(isAuthenticated => {
            if (isAuthenticated) {
                router.navigate(['/dashboard']);
            }

            return true;
        }),
    );
};
