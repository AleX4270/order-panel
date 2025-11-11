import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/http.types';
import { PriorityFilterParams, PriorityItem } from '../../../types/priority.types';
import { RestService } from '../rest/rest.service';
import { IndexResponse } from '../../../types/rest.types';

@Injectable({
    providedIn: 'root'
})
export class PriorityService extends RestService {
    public index(params?: PriorityFilterParams): Observable<ApiResponse<IndexResponse<PriorityItem>>> {
        return this.http.get<ApiResponse<IndexResponse<PriorityItem>>>(`${this.apiUrl}/priorities?=${this.getQueryParams(params)}`);
    }
}
