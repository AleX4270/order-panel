import { Component, effect, inject, signal } from '@angular/core';
import { NotificationDrawerService } from '../../services/notification-drawer/notification-drawer.service';
import { TranslatePipe } from '@ngx-translate/core';
import { UserNotificationService } from '../../services/user-notification/user-notification.service';
import { NotificationComponent } from '../notification/notification.component';

@Component({
    selector: 'app-notification-drawer',
    imports: [
        TranslatePipe,
        NotificationComponent,
    ],
    template: `
        <div class="drawer drawer-end">
            <input [checked]="notificationDrawerService.drawerState()" id="notification-drawer" type="checkbox" class="drawer-toggle" />
            <div class="drawer-content">
                <ng-content></ng-content>
            </div>
            <div class="drawer-side opacity-95">
                <div class="bg-base-100 min-h-full w-100 p-4">
                    <h1 class="font-semibold text-xl mb-5 mt-2">{{'notificationDrawer.title' | translate}}</h1>
                    <div class="flex flex-col items-center gap-4">
                        @for(notification of userNotificationService.notifications(); track notification) {
                            <app-notification 
                                [notification]="notification"
                            />
                        }
                        @empty {
                            <small class="text-neutral/50 text-xs">{{'notificationDrawer.noNotifications' | translate}}</small>
                        }
                    </div>
                </div>
            </div>
        </div>
    `,
    styles: [`
        .drawer-side {
            top: var(--navbar-height, 0px);
        }
    `]
})
export class NotificationDrawerComponent {
    protected readonly notificationDrawerService = inject(NotificationDrawerService);
    protected readonly userNotificationService = inject(UserNotificationService);
}
