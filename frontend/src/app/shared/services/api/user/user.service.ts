import { Injectable } from '@angular/core';
import { RestService } from '../rest/rest.service';
import { UserFilterParams, UserItem, UserParams } from '../../../types/user.types';
import { Observable } from 'rxjs';
import { ApiResponse, IndexResponse } from '../../../types/rest.types';
import { PAGINATION_PAGE_SIZE, PAGINATION_START_PAGE } from '../../../../app.constants';

@Injectable({
    providedIn: 'root'
})
export class UserService extends RestService {
    public index(params: UserFilterParams): Observable<ApiResponse<IndexResponse<UserItem>>> {
        const defaultParams = {
            page: PAGINATION_START_PAGE,
            pageSize: PAGINATION_PAGE_SIZE,
        } as UserFilterParams;

        const paramsCopy = {
            ...defaultParams,
            ...params
        } as UserFilterParams;

        return this.http.get<ApiResponse<IndexResponse<UserItem>>>(`${this.apiUrl}/users${this.getQueryParams(paramsCopy)}`);
    }

    public show(userId: number): Observable<ApiResponse<UserItem>> {
        return this.http.get<ApiResponse<UserItem>>(`${this.apiUrl}/users/${userId}`);
    }

    public store(params: UserParams): Observable<ApiResponse<void>> {
        return this.http.post<ApiResponse<void>>(`${this.apiUrl}/users`, params);
    }

    public update(params: UserParams): Observable<ApiResponse<void>> {
        return this.http.put<ApiResponse<void>>(`${this.apiUrl}/users`, params);
    }

    public delete(userId: number): Observable<ApiResponse<void>> {
        return this.http.delete<ApiResponse<void>>(`${this.apiUrl}/users/${userId}`);
    }
}
