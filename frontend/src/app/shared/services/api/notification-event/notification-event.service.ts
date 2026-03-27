import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/rest.types';
import { RestService } from '../rest/rest.service';
import { IndexResponse } from '../../../types/rest.types';
import { NotificationEventFilterParams, NotificationEventItem } from '../../../types/notification-event.types';

@Injectable({
    providedIn: 'root'
})
export class NotificationEventService extends RestService {
    public index(params?: NotificationEventFilterParams): Observable<ApiResponse<NotificationEventItem[]>> {
        return this.http.get<ApiResponse<NotificationEventItem[]>>(`${this.apiUrl}/notification-events${this.getQueryParams(params)}`);
    }
}
