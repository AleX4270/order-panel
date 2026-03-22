import { Component, inject, input } from '@angular/core';
import { Notification } from '../../types/notification.types';
import { DatePipe } from '@angular/common';
import { NgIcon, provideIcons } from '@ng-icons/core';
import { faSolidX } from '@ng-icons/font-awesome/solid';
import { NotificationService } from '../../services/api/notification/notification.service';
import { UserNotificationService } from '../../services/user-notification/user-notification.service';

@Component({
    selector: 'app-notification',
    imports: [
        DatePipe,
        NgIcon
    ],
    template: `
        <div class="indicator w-full">
            <span class="indicator-item badge badge-xs badge-neutral badge-soft cursor-pointer" (click)="markAsRead()">
                <ng-icon
                    name="faSolidX"
                    size="8px"
                ></ng-icon>
            </span>
            <div class="card card-border w-full bg-base-100 card-xs shadow-md">
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <h2 class="card-title text-neutral/50">{{notification().title}}</h2>
                        <span class="text-[10px] text-neutral/50">{{notification().createdAt | date:'dd-MM-yyyy HH:mm'}}</span>
                    </div>
                    <p>{{notification().message}}</p>
                </div>
            </div>
        </div>
    `,
    styles: [``],
    providers: [provideIcons({faSolidX})],
})
export class NotificationComponent {
    private readonly userNotificationService = inject(UserNotificationService);
    public notification = input.required<Notification>();

    protected markAsRead(): void {
        console.log('ol');
        this.userNotificationService.markAsRead(this.notification().id);
    }
}
