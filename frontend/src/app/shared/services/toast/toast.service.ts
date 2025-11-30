import { Injectable } from '@angular/core';
import { Toast } from '../../types/toast.types';
import { Observable, Subject } from 'rxjs';
import { MAX_TOAST_STACK_ELEMENTS, TOAST_DURATION } from '../../../app.constants';
import { ToastType } from '../../enums/enums';

@Injectable({
    providedIn: 'root'
})

export class ToastService {
    private toastStack: Toast[] = [];
    private readonly toastStackSubject$: Subject<Toast[]> = new Subject<Toast[]>();
    private toastId: number = 0;
    
    public readonly toastStack$: Observable<Toast[]> = this.toastStackSubject$.asObservable();

    public show(message: string, type: ToastType, duration: number = TOAST_DURATION): void {
        const toast = {
            id: ++this.toastId,
            message: message,
            type: type,
            duration: duration,
        } as Toast;

        if(this.toastStack.length >= MAX_TOAST_STACK_ELEMENTS) {
            this.toastStack.splice(0, 1);
        }

        this.toastStack.push(toast);
        this.toastStackSubject$.next(this.toastStack);
    }

    public dismiss(toastId: number): void {
        const dismissedToastIndex = this.toastStack.findIndex((existingToast) => {
            return existingToast.id === toastId;
        });

        if(dismissedToastIndex >= 0) {
            this.toastStack.splice(dismissedToastIndex, 1);
            this.toastStackSubject$.next(this.toastStack);
        }
    }
}
