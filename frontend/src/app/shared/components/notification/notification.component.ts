import { Component, input } from '@angular/core';
import { Notification } from '../../types/notification.types';

@Component({
    selector: 'app-notification',
    imports: [],
    template: `
        <div class="indicator">
            <span class="indicator-item indicator-start status status-error"></span>
            <div class="card card-border w-96 bg-base-100 card-xs shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">{{notification().title}}</h2>
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
