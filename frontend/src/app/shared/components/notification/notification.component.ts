import { Component, input } from '@angular/core';
import { Notification } from '../../types/notification.types';
import { DatePipe } from '@angular/common';

@Component({
    selector: 'app-notification',
    imports: [
        DatePipe
    ],
    template: `
        <div class="indicator">
            <!-- <span class="indicator-item indicator-start badge badge-xs badge-error"></span> -->
            <div class="card card-border w-96 bg-base-100 card-xs shadow-sm">
                <div class="card-body">
                    <div class="flex justify-between items-center">
                        <h2 class="card-title text-neutral/50">{{notification().title}}</h2>
                        <span class="text-[10px] text-neutral/50">{{notification().createdAt | date:'dd-MM-yyyy h:mm'}}</span>
                    </div>
                    <p>{{notification().message}}</p>
                </div>
            </div>
        </div>
    `,
    styles: [``],
})
export class NotificationComponent {
    public notification = input.required<Notification>();
}
