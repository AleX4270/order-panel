import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/http.types';
import { RestService } from '../rest/rest.service';
import { OrderParams } from './order.types';

@Injectable({
    providedIn: 'root'
})
export class OrderService extends RestService {
    public store(params: OrderParams): Observable<ApiResponse<void>> {
        return this.http.post<ApiResponse<void>>(`${this.apiUrl}/orders`, params);
    }
}
