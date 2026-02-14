import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';
import { environment } from '../../../environments/environment';

export const csrfInterceptor: HttpInterceptorFn = (req, next) => {
    const cookieService = inject(CookieService);

    const xsrfToken = cookieService.get('XSRF-TOKEN');

    if(xsrfToken && req.url.startsWith(environment.apiUrl)) {
        req = req.clone({
            withCredentials: true,
            headers: req.headers.set('X-XSRF-TOKEN', decodeURIComponent(xsrfToken))
        });
    }

    return next(req);
};
