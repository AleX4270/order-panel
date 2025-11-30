import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse, IndexResponse } from '../../../types/rest.types';
import { RestService } from '../rest/rest.service';
import { OrderFilterParams, OrderItem, OrderParams } from '../../../types/order.types';
import { PAGINATION_PAGE_SIZE, PAGINATION_START_PAGE } from '../../../../app.constants';

@Injectable({
    providedIn: 'root'
})
export class OrderService extends RestService {
    public index(params: OrderFilterParams): Observable<ApiResponse<IndexResponse<OrderItem>>> {
        const defaultParams = {
            page: PAGINATION_START_PAGE,
            pageSize: PAGINATION_PAGE_SIZE,
        } as OrderFilterParams;

        const paramsCopy = {
            ...defaultParams,
            ...params
        } as OrderFilterParams;

        return this.http.get<ApiResponse<IndexResponse<OrderItem>>>(`${this.apiUrl}/orders${this.getQueryParams(paramsCopy)}`);
    }

    public show(orderId: number): Observable<ApiResponse<OrderItem>> {
        return this.http.get<ApiResponse<OrderItem>>(`${this.apiUrl}/orders/${orderId}`);
    }

    public store(params: OrderParams): Observable<ApiResponse<void>> {
        return this.http.post<ApiResponse<void>>(`${this.apiUrl}/orders`, params);
    }

    public update(params: OrderParams): Observable<ApiResponse<void>> {
        return this.http.put<ApiResponse<void>>(`${this.apiUrl}/orders`, params);
    }

    public delete(orderId: number): Observable<ApiResponse<void>> {
        return this.http.delete<ApiResponse<void>>(`${this.apiUrl}/orders/${orderId}`);
    }
}
