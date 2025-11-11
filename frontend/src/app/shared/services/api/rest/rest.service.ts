import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../../../environments/environment';

@Injectable({
    providedIn: 'root'
})
export class RestService {
    protected readonly http = inject(HttpClient);
    protected readonly apiUrl = environment.apiUrl;

    protected getQueryParams(params: Object | null | undefined): string {
        if(params == null || params == undefined) {
            return '';
        }

        return '?=' + Object.entries(params)
            .map(([key, value]) => `${key}=${value}`)
            .join('&');
    }
}
