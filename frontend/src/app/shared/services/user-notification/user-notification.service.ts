import { computed, inject, Injectable, Signal, signal, WritableSignal } from '@angular/core';
import { BroadcastService } from '../broadcast/broadcast.service';
import { Store } from '@ngxs/store';
import { UserState } from '../../store/user/user.state';
import { ChannelType } from '../../types/broadcast.types';
import { Notification } from '../../types/notification.types';
import { NotificationService } from '../api/notification/notification.service';

@Injectable({
    providedIn: 'root',
})
export class UserNotificationService {
    private readonly USER_CHANNEL_PREFIX = 'users';

    private readonly notificationService = inject(NotificationService);
    private readonly broadcastService = inject(BroadcastService);
    private readonly store = inject(Store);

    private readonly userId = this.store.selectSignal(UserState.userId);
    private readonly userChannel = computed(() => this.USER_CHANNEL_PREFIX + '.' + this.userId());

    private _notifications: WritableSignal<Notification[]> = signal<Notification[]>([]);

    // private _notifications: WritableSignal<Notification[]> = signal<Notification[]>([
    //     {
    //         id: '1',
    //         title: 'Test',
    //         message: 'Message',
    //         createdAt: '2026-03-22T11:42:02.859219Z',
    //         type: 'type',
    //         readAt: null,
    //     }
    // ]);

    public readonly notifications: Signal<Notification[]> = this._notifications.asReadonly();
    public readonly newNotificationsCount: Signal<number> = computed(() => this._notifications().filter((notification) => notification.readAt === null).length);

    public connectToChannel(): void {
        if(this.userId() === null) {
            console.error('Cannot connect to the user\'s notification channel. User ID is unknown');
            return;
        }

        // TEMP
        this.loadExistingNotifications();

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
        this._notifications.update((notifications) => [
            data,
            ...notifications
        ]);
    }

    private loadExistingNotifications(): void {
        const userId = this.userId();
        
        if(userId === null) {
            return;
        }

        this.notificationService.index({ userId: userId }).subscribe({
            next: (res) => {
                console.log(res);      
            },
            error: (err) => {
                console.error(err);
            }
        });
    }
}
