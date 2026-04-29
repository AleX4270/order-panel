import { Component, computed, input, InputSignal } from '@angular/core';
import { AlertType } from '../../types/alert.types';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'app-alert',
    imports: [CommonModule],
    template: `
        <div role="alert" [ngClass]="classList()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="h-6 w-6 shrink-0 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <ng-content></ng-content>
        </div>
    `,
    styles: [``]
})
export class AlertComponent {
    public type: InputSignal<AlertType> = input<AlertType>('info');
    public isSoft: InputSignal<boolean> = input<boolean>(false);
    public isOutlined: InputSignal<boolean> = input<boolean>(false);

    protected classList = computed(() => {
        let list = 'alert';

        if(this.isSoft()) {
            list += ' alert-soft';
        }

        if(this.isOutlined()) {
            list += ' alert-outline';
        }

        switch(this.type()) {
            case 'info':
                list += ' alert-info';
                break;
            case 'success':
                list += ' alert-success';
                break;
            case 'warning':
                list += ' alert-warning';
                break;
            case 'error':
                list += ' alert-error';
                break;
            default:
                list += ' alert-info';
                break;
        }

        return list;
    });
}
