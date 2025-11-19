import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/http.types';
import { RestService } from '../rest/rest.service';
import { OrderFilterParams, OrderParams } from '../../../types/order.types';

@Injectable({
    providedIn: 'root'
})
export class OrderService extends RestService {
    public index(params: OrderFilterParams): Observable<ApiResponse<void>> {
        return this.http.get<ApiResponse<void>>(`${this.apiUrl}/orders${this.getQueryParams(params)}`);
    }

    public store(params: OrderParams): Observable<ApiResponse<void>> {
        return this.http.post<ApiResponse<void>>(`${this.apiUrl}/orders`, params);
    }
}
