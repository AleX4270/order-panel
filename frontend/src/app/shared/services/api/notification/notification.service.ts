import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/rest.types';
import { RestService } from '../rest/rest.service';
import { IndexResponse } from '../../../types/rest.types';
import { Notification, NotificationFilterParams } from '../../../types/notification.types';

@Injectable({
    providedIn: 'root'
})
export class NotificationService extends RestService {
    public index(params?: NotificationFilterParams): Observable<ApiResponse<IndexResponse<Notification>>> {
        return this.http.get<ApiResponse<IndexResponse<Notification>>>(`${this.apiUrl}/notifications${this.getQueryParams(params)}`);
    }
}
