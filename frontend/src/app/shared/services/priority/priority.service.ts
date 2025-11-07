import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../types/http.types';
import { PriorityFilterParams } from '../../types/priority.types';
import { RestService } from '../rest/rest.service';

@Injectable({
    providedIn: 'root'
})
export class PriorityService extends RestService {
    public index(params?: PriorityFilterParams): Observable<ApiResponse<void>> {
        return this.http.get<ApiResponse<void>>(`${this.apiUrl}/priorities?=${this.getQueryParams(params)}`);
    }
}
