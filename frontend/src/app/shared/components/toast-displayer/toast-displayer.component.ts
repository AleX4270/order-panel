import { Component, inject, OnDestroy, OnInit } from '@angular/core';
import { ToastService } from '../../services/toast/toast.service';
import { ToastComponent } from '../toast/toast.component';
import { Toast } from '../../types/toast.types';
import { Subscription } from 'rxjs';

@Component({
    selector: 'app-toast-displayer',
    imports: [
        ToastComponent,
    ],
    template: `
        @for(toast of toastStack; track toast) {
            <app-toast
                [toastData]="toast"
                (dismiss)="onToastDismiss($event)"
            />
        }
    `,
    styles: `
        :host {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            z-index: 1050;
        }
    `
})
export class ToastDisplayerComponent implements OnDestroy, OnInit {
    private readonly toastService: ToastService = inject(ToastService);
    private toastServiceSubscription!: Subscription;
    
    protected toastStack: Toast[] = [];

    ngOnInit(): void {
        this.toastServiceSubscription = this.toastService.toastStack$.subscribe({
            next: (val: any) => {
                this.toastStack = val;
            },
        });
    }

    protected onToastDismiss(toastId: number): void {
        this.toastService.dismiss(toastId);
    }

    ngOnDestroy(): void {
        this.toastServiceSubscription.unsubscribe();
    }
}
