import { computed, inject, Injectable, Signal, signal, WritableSignal } from '@angular/core';
import { BroadcastService } from '../broadcast/broadcast.service';
import { Store } from '@ngxs/store';
import { UserState } from '../../store/user/user.state';
import { ChannelType } from '../../types/broadcast.types';
import { Notification } from '../../types/notification.types';

@Injectable({
    providedIn: 'root',
})
export class UserNotificationService {
    private readonly USER_CHANNEL_PREFIX = 'users';

    private readonly broadcastService = inject(BroadcastService);
    private readonly store = inject(Store);

    private readonly userId = this.store.selectSignal(UserState.userId);
    private readonly userChannel = computed(() => this.USER_CHANNEL_PREFIX + '.' + this.userId());

    private _notifications: WritableSignal<Notification[]> = signal<Notification[]>([]);
    public readonly notifications: Signal<Notification[]> = this._notifications.asReadonly();

    public connectToChannel(): void {
        if(this.userId() === null) {
            console.error('Cannot connect to the user\'s notification channel. User ID is unknown');
            return;
        }

        this.broadcastService.listenToChannel<Notification>(this.userChannel(), ChannelType.private, (_eventName, data) => this.onNotificationReceived(data));
    }

    public disconnectFromChannel(): void {
        if(this.userId() === null) {
            console.error('Cannot disconnect from the user\'s notification channel. User ID is unknown');
            return;
        }

        this.broadcastService.leaveChannel(this.userChannel());
    }

    public onNotificationReceived(data: Notification): void {
        console.log(data);
        // TODO: Finish
    }
}
