import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse, IndexResponse } from '../../../types/rest.types';
import { RestService } from '../rest/rest.service';
import { OrderFilterParams, OrderItem, OrderParams } from '../../../types/order.types';

@Injectable({
    providedIn: 'root'
})
export class OrderService extends RestService {
    public index(params: OrderFilterParams): Observable<ApiResponse<IndexResponse<OrderItem>>> {
        return this.http.get<ApiResponse<IndexResponse<OrderItem>>>(`${this.apiUrl}/orders${this.getQueryParams(params)}`);
    }

    public store(params: OrderParams): Observable<ApiResponse<void>> {
        return this.http.post<ApiResponse<void>>(`${this.apiUrl}/orders`, params);
    }
}
