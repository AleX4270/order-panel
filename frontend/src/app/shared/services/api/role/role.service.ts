import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/rest.types';
import { RestService } from '../rest/rest.service';
import { IndexResponse } from '../../../types/rest.types';
import { RoleFilterParams, RoleItem } from '../../../types/role.types';

@Injectable({
    providedIn: 'root'
})
export class RoleService extends RestService {
    public index(params?: RoleFilterParams): Observable<ApiResponse<IndexResponse<RoleItem>>> {
        return this.http.get<ApiResponse<IndexResponse<RoleItem>>>(`${this.apiUrl}/roles${this.getQueryParams(params)}`);
    }
}
