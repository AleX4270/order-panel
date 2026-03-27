import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/rest.types';
import { RestService } from '../rest/rest.service';
import { IndexResponse } from '../../../types/rest.types';
import { NotificationChannelFilterParams, NotificationChannelItem } from '../../../types/notification-channel.types';

@Injectable({
    providedIn: 'root'
})
export class NotificationChannelService extends RestService {
    public index(params?: NotificationChannelFilterParams): Observable<ApiResponse<NotificationChannelItem[]>> {
        return this.http.get<ApiResponse<NotificationChannelItem[]>>(`${this.apiUrl}/notification-channels${this.getQueryParams(params)}`);
    }
}
