import { Component, ElementRef, inject, OnDestroy, OnInit } from '@angular/core';
import { ToastService } from '../../services/toast/toast.service';
import { ToastComponent } from '../toast/toast.component';
import { Toast } from '../../types/toast.types';
import { Subscription } from 'rxjs';

@Component({
    selector: 'app-toast-displayer',
    imports: [
        ToastComponent,
    ],
    host: {
        popover: 'manual',
    },
    template: `
        <div class="toast">
            @for(toast of toastStack; track toast) {
                <app-toast
                    [toastData]="toast"
                    (dismiss)="onToastDismiss($event)"
                />
            }
        </div>
    `,
    styles: `
        :host {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            inset: unset;
            border: none;
            background: transparent;
            padding: 0;
            margin: 0;
        }
    `
})
export class ToastDisplayerComponent implements OnDestroy, OnInit {
    private readonly toastService: ToastService = inject(ToastService);
    private readonly elementRef: ElementRef<HTMLElement> = inject(ElementRef);
    private toastServiceSubscription!: Subscription;

    protected toastStack: Toast[] = [];

    ngOnInit(): void {
        this.toastServiceSubscription = this.toastService.toastStack$.subscribe({
            next: (val: any) => {
                this.toastStack = val;
                if (val.length > 0) {
                    this.bringToTopLayer();
                }
            },
        });
    }

    private bringToTopLayer(): void {
        const el = this.elementRef.nativeElement as any;
        if (el.hidePopover) el.hidePopover();
        if (el.showPopover) el.showPopover();
    }

    protected onToastDismiss(toastId: number): void {
        this.toastService.dismiss(toastId);
    }

    ngOnDestroy(): void {
        this.toastServiceSubscription.unsubscribe();
    }
}
