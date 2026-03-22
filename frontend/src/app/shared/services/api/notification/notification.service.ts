import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/rest.types';
import { RestService } from '../rest/rest.service';
import { Notification, NotificationFilterParams } from '../../../types/notification.types';

@Injectable({
    providedIn: 'root'
})
export class NotificationService extends RestService {
    public index(params?: NotificationFilterParams): Observable<ApiResponse<Notification[]>> {
        return this.http.get<ApiResponse<Notification[]>>(`${this.apiUrl}/notifications${this.getQueryParams(params)}`);
    }

    public markAsRead(id: string): Observable<ApiResponse> {
        return this.http.post<ApiResponse>(`${this.apiUrl}/notifications/mark-as-read`, {id});
    }
}
