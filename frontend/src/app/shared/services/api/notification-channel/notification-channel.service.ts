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
    public list(params?: NotificationChannelFilterParams): Observable<ApiResponse<IndexResponse<NotificationChannelItem>>> {
        return this.http.get<ApiResponse<IndexResponse<NotificationChannelItem>>>(`${this.apiUrl}/notification-channels${this.getQueryParams(params)}`);
    }
}
