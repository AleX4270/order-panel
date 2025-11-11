import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/http.types';
import { RestService } from '../rest/rest.service';
import { IndexResponse } from '../../../types/rest.types';
import { ProvinceFilterParams, ProvinceItem } from '../../../types/province.types';

@Injectable({
    providedIn: 'root'
})
export class ProvinceService extends RestService {
    public index(params?: ProvinceFilterParams): Observable<ApiResponse<IndexResponse<ProvinceItem>>> {
        return this.http.get<ApiResponse<IndexResponse<ProvinceItem>>>(`${this.apiUrl}/provinces${this.getQueryParams(params)}`);
    }
}
