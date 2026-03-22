import { computed, inject, Injectable, Signal, signal, WritableSignal } from '@angular/core';
import { BroadcastService } from '../broadcast/broadcast.service';
import { Store } from '@ngxs/store';
import { UserState } from '../../store/user/user.state';
import { ChannelType } from '../../types/broadcast.types';
import { Notification } from '../../types/notification.types';
import { NotificationService } from '../api/notification/notification.service';
import { tap } from 'rxjs';
import { ToastService } from '../toast/toast.service';
import { ToastType } from '../../enums/enums';
import { TranslateService } from '@ngx-translate/core';

@Injectable({
    providedIn: 'root',
})
export class UserNotificationService {
    private readonly USER_CHANNEL_PREFIX = 'users';

    private readonly notificationService = inject(NotificationService);
    private readonly broadcastService = inject(BroadcastService);
    private readonly store = inject(Store);
    private readonly toastService = inject(ToastService);
    private readonly translateService = inject(TranslateService);

    private readonly userId = this.store.selectSignal(UserState.userId);
    private readonly userChannel = computed(() => this.USER_CHANNEL_PREFIX + '.' + this.userId());

    private _notifications: WritableSignal<Notification[]> = signal<Notification[]>([]);

    public readonly notifications: Signal<Notification[]> = this._notifications.asReadonly();
    public readonly newNotificationsCount: Signal<number> = computed(() => this._notifications().filter((notification) => notification.readAt === null).length);

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
        this._notifications.update((notifications) => [
            data,
            ...notifications
        ]);

        this.toastService.show(this.translateService.instant('notification.newNotificationReceived'), ToastType.info);
    }

    public markAsRead(id: string): void {
        this.notificationService.markAsRead(id).pipe(
            tap(() => this.loadUnreadNotifications()),
        ).subscribe();
    }

    public loadUnreadNotifications(): void {
        const userId = this.userId();
        
        if(userId === null) {
            console.error('Cannot load the user\'s unread notifications list. User ID is unknown');
            return;
        }

        this.notificationService.index({ userId: userId, onlyUnread: true }).subscribe({
            next: (res) => {
                const unreadNotifications = res.data;
                if(unreadNotifications) {
                    this._notifications.set(unreadNotifications);
                }
            },
            error: (err) => {
                console.error(err);
            }
        });
    }
}
