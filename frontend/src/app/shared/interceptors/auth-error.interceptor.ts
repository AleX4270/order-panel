import { HttpErrorResponse, HttpInterceptorFn } from '@angular/common/http';
import { catchError, switchMap, tap, throwError } from 'rxjs';
import { HttpStatus, ToastType } from '../enums/enums';
import { inject } from '@angular/core';
import { Store } from '@ngxs/store';
import { LogoutUser } from '../store/user/user.actions';
import { Router } from '@angular/router';
import { ToastService } from '../services/toast/toast.service';
import { TranslateService } from '@ngx-translate/core';

export const authErrorInterceptor: HttpInterceptorFn = (req, next) => {
    const store = inject(Store);
    const router = inject(Router);
    const toastService = inject(ToastService);
    const translateService = inject(TranslateService);

    return next(req).pipe(
        catchError((error: HttpErrorResponse) => {
            if(error.status === HttpStatus.UNAUTHORIZED || error.status === HttpStatus.PAGE_EXPIRED) {
                return store.dispatch(new LogoutUser())
                    .pipe(
                        tap(() => {
                            toastService.show(translateService.instant('basic.sessionExpired'), ToastType.warning);
                            return router.navigate(['/']);
                        }),
                        switchMap(() => throwError(() => error))
                    );
            }

            return throwError(() => error);
        })
    );
};
