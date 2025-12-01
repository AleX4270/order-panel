import { Injectable } from '@angular/core';
import { RestService } from '../rest/rest.service';
import { UserFilterParams, UserItem } from '../../../types/user.types';
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

    // public show(orderId: number): Observable<ApiResponse<OrderItem>> {
    //     return this.http.get<ApiResponse<OrderItem>>(`${this.apiUrl}/orders/${orderId}`);
    // }

    // public store(params: OrderParams): Observable<ApiResponse<void>> {
    //     return this.http.post<ApiResponse<void>>(`${this.apiUrl}/orders`, params);
    // }

    // public update(params: OrderParams): Observable<ApiResponse<void>> {
    //     return this.http.put<ApiResponse<void>>(`${this.apiUrl}/orders`, params);
    // }

    // public delete(orderId: number): Observable<ApiResponse<void>> {
    //     return this.http.delete<ApiResponse<void>>(`${this.apiUrl}/orders/${orderId}`);
    // }
}
