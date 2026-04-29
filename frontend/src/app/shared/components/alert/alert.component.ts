import { Component, computed, input, InputSignal } from '@angular/core';
import { AlertType } from '../../types/alert.types';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'app-alert',
    imports: [CommonModule],
    template: `
        <div role="alert" [ngClass]="classList()">
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
