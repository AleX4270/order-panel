import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/rest.types';
import { RestService } from '../rest/rest.service';
import { IndexResponse } from '../../../types/rest.types';
import { CountryFilterParams, CountryItem } from '../../../types/country.types';

@Injectable({
    providedIn: 'root'
})
export class CountryService extends RestService {
    public index(params?: CountryFilterParams): Observable<ApiResponse<IndexResponse<CountryItem>>> {
        return this.http.get<ApiResponse<IndexResponse<CountryItem>>>(`${this.apiUrl}/countries${this.getQueryParams(params)}`);
    }
}
