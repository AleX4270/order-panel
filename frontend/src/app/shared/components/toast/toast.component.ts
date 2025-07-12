import { Component, effect, input, output } from '@angular/core';
import { Toast } from '../../types/toast.types';

@Component({
    selector: 'app-toast',
    imports: [],
    template: `
        <div>
            <div class="toast-card shadow-sm mt-1 bg-white">
                <div class="toast-card-header pb-1 d-flex justify-content-end">
                    <!-- <h6>{{toastData().title}}</h6> -->
                    <button type="button" class="btn btn-close" (click)="onDismiss(toastData().id)"></button>
                </div>
                <div class="toast-card-body pt-2">
                    <p class="text-muted small">{{toastData().message + toastData().id}}</p>
                </div>
            </div>
        </div>
    `,
    styles: `
        .toast-card {
            border: 1px solid #cce5ff;
            border-radius: 10px;
            min-width: 350px;
            padding: 12px;
        }

        .toast-card-header {
            border-bottom: 2px solid red;
        }
    `
})
export class ToastComponent {
    public toastData = input.required<Toast>();
    public dismiss = output<number>();

    constructor() {
        effect(() => {
            setTimeout(() => {
                this.dismiss.emit(this.toastData().id);
            }, this.toastData().duration);
        });
    }

    public onDismiss(toastId: number) {
        this.dismiss.emit(toastId);
    }
}
