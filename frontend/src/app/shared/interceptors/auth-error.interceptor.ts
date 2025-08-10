import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { catchError, throwError } from 'rxjs';
import { HttpStatus } from '../enums/enums';
import { inject } from '@angular/core';
import { Store } from '@ngxs/store';
import { LogoutUser } from '../store/user/user.actions';

export const authErrorInterceptor: HttpInterceptorFn = (req, next) => {
    const store = inject(Store);

    return next(req).pipe(
        catchError((error: HttpErrorResponse) => {
            if(error.status === HttpStatus.UNAUTHORIZED || error.status === HttpStatus.PAGE_EXPIRED) {
                store.dispatch(new LogoutUser());
                return throwError(() => error);
            }

            return throwError(() => error);
        })
    );
};
