import { Component, inject } from '@angular/core';
import { NgIcon, provideIcons } from '@ng-icons/core';
import { faBell } from '@ng-icons/font-awesome/regular';
import { NotificationDrawerService } from '../../services/notification-drawer/notification-drawer.service';
import { UserNotificationService } from '../../services/user-notification/user-notification.service';

@Component({
    selector: 'app-notification-icon',
    imports: [
        NgIcon,
    ],
    template: `
        <div class="hover:bg-black/6 hover:cursor-pointer rounded-sm p-1 pb-0" (click)="toggleDrawer()">
            <div class="indicator">
                @if(userNotificationService.newNotificationsCount() > 0) {
                    <span class="indicator-item indicator-start badge badge-xs badge-error">{{userNotificationService.newNotificationsCount()}}</span>
                }
                <ng-icon
                    name="faBell"
                    size="19px"
                ></ng-icon>
            </div>
        </div>
    `,
    styles: [``],
    providers: [provideIcons({ faBell })],
})
export class NotificationIconComponent {
    private readonly notificationDrawerService = inject(NotificationDrawerService);
    protected readonly userNotificationService = inject(UserNotificationService);

    protected toggleDrawer(): void {
        this.notificationDrawerService.toggle();
    }
}
