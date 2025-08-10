import { Component, effect, input, output, signal } from '@angular/core';
import { Toast } from '../../types/toast.types';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'app-toast',
    imports: [
        CommonModule
    ],
    template: `
        <div>
            <!-- TODO! -->
            <div
                class="toast-card toast-success shadow-sm mt-1"
                [ngClass]="{'fade-out': isFadingOut()}"
            >
                <div class="toast-card-body pt-3">
                    <p class="small">{{toastData().message}}</p>
                </div>
            </div>
        </div>
    `,
    styles: `
        .toast-card {
            border: 1px solid #cce5ff;
            border-radius: 10px;
            width: 350px;
            padding: 12px;
            display: flex;
            justify-content: center;

            animation: fade-in 0.5s ease-in;

            .toast-card-header {
                border-bottom: 2px solid lightgray;
            }

            &.fade-out {
                animation: fade-out 0.5s ease-in forwards;
            }

            &.toast-info {
                background-color: var(--toast-info-bg);
                color: var(--toast-info-text);
                .toast-card-header {
                    border-bottom: 2px solid var(--toast-info-separator);
                }
            }

            &.toast-success {
                background-color: var(--toast-success-bg);
                color: var(--toast-success-text);
                .toast-card-header {
                    border-bottom: 2px solid var(--toast-success-separator);
                }
            }

            &.toast-warning {
                background-color: var(--toast-warning-bg);
                color: var(--toast-warning-text);
                .toast-card-header {
                    border-bottom: 2px solid var(--toast-warning-separator);
                }
            }

            &.toast-danger {
                background-color: var(--toast-danger-bg);
                color: var(--toast-danger-text);
                .toast-card-header {
                    border-bottom: 2px solid var(--toast-danger-separator);
                }
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

    public toastData = input.required<Toast>();
    public dismiss = output<number>();

    constructor() {
        effect(() => {
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
