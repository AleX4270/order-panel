import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/http.types';
import { RestService } from '../rest/rest.service';
import { IndexResponse } from '../../../types/rest.types';
import { CityFilterParams, CityItem } from '../../../types/city.types';

@Injectable({
    providedIn: 'root'
})
export class CityService extends RestService {
    public index(params?: CityFilterParams): Observable<ApiResponse<IndexResponse<CityItem>>> {
        return this.http.get<ApiResponse<IndexResponse<CityItem>>>(`${this.apiUrl}/cities${this.getQueryParams(params)}`);
    }
}
