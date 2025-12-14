import { Component, effect, input, output, signal } from '@angular/core';
import { Toast } from '../../types/toast.types';
import { CommonModule } from '@angular/common';
import { ToastType } from '../../enums/enums';

@Component({
    selector: 'app-toast',
    imports: [
        CommonModule
    ],
    template: `
        <div role="alert" class="alert alert-soft"
            [ngClass]="{
                'alert-info': toastData().type === toastType.info,
                'alert-success': toastData().type === toastType.success,
                'alert-warning': toastData().type === toastType.warning,
                'alert-error': toastData().type === toastType.danger
            }"
        >
            <span>{{toastData().message}}</span>
        </div>
    `,
    styles: `
        .toast-card {
            border-radius: var(--radius-lg);
            width: 350px;   
            padding: 12px;
            display: flex;
            justify-content: center;

            animation: fade-in 0.3s ease-in;

            &.fade-out {
                animation: fade-out 0.3s ease-in forwards;
            }
        }

        @keyframes fade-in {
            from { opacity: 0; transform: translateX(100%); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes fade-out {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(100%); }
        }
    `
})
export class ToastComponent {
    protected isFadingOut = signal<boolean>(false);
    protected toastType = ToastType;

    public toastData = input.required<Toast>();
    public dismiss = output<number>();

    constructor() {
        effect(() => {
            // TODO: Add the not auto dismissable toast option
            setTimeout(() => {
                this.onDismiss(this.toastData().id);
            }, this.toastData().duration);
        });
    }

    public onDismiss(toastId: number) {
        this.isFadingOut.set(true);
        setTimeout(() => {
            this.dismiss.emit(toastId);
        }, 500);
    }
}
