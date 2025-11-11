import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/http.types';
import { RestService } from '../rest/rest.service';
import { IndexResponse } from '../../../types/rest.types';
import { StatusFilterParams, StatusItem } from '../../../types/status.types';

@Injectable({
    providedIn: 'root'
})
export class StatusService extends RestService {
    public index(params?: StatusFilterParams): Observable<ApiResponse<IndexResponse<StatusItem>>> {
        return this.http.get<ApiResponse<IndexResponse<StatusItem>>>(`${this.apiUrl}/statuses?=${this.getQueryParams(params)}`);
    }
}
