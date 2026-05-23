import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse, IndexResponse } from '../../../types/rest.types';
import { RestService } from '../rest/rest.service';
import { OrderRequestFilterParams, OrderRequestItem, OrderRequestParams } from '../../../types/order-request.types';
import { PAGINATION_PAGE_SIZE, PAGINATION_START_PAGE } from '../../../../app.constants';

@Injectable({
    providedIn: 'root'
})
export class OrderRequestService extends RestService {
    public index(params: OrderRequestFilterParams): Observable<ApiResponse<IndexResponse<OrderRequestItem>>> {
        const defaultParams = {
            page: PAGINATION_START_PAGE,
            pageSize: PAGINATION_PAGE_SIZE,
        } as OrderRequestFilterParams;

        const paramsCopy = {
            ...defaultParams,
            ...params
        } as OrderRequestFilterParams;

        return this.http.get<ApiResponse<IndexResponse<OrderRequestItem>>>(`${this.apiUrl}/order-requests${this.getQueryParams(paramsCopy)}`);
    }

    public store(params: OrderRequestParams): Observable<ApiResponse<void>> {
        return this.http.post<ApiResponse<void>>(`${this.apiUrl}/order-requests`, params);
    }

    public castToOrder(orderRequestId: number): Observable<ApiResponse<void>> {
        return this.http.post<ApiResponse<void>>(`${this.apiUrl}/order-requests/cast-to-order`, { id: orderRequestId });
    }

    public delete(orderRequestId: number): Observable<ApiResponse<void>> {
        return this.http.delete<ApiResponse<void>>(`${this.apiUrl}/order-requests/${orderRequestId}`);
    }
}
