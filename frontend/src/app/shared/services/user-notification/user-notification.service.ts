import { inject, Injectable, Signal, signal, WritableSignal } from '@angular/core';
import { BroadcastService } from '../broadcast/broadcast.service';
import { Store } from '@ngxs/store';

@Injectable({
    providedIn: 'root',
})
export class UserNotificationService {
    private readonly USER_CHANNEL_PREFIX = 'users';

    private readonly broadcastService = inject(BroadcastService);
    private readonly store = inject(Store);

    private _notifications: WritableSignal<Notification[]> = signal<Notification[]>([]);
    public readonly notifications: Signal<Notification[]> = this._notifications.asReadonly();

    public connectToChannel(): void {

    }

    public disconnectFromChannel(): void {

    }

    public onNotificationReceived(): void {

    }
}
