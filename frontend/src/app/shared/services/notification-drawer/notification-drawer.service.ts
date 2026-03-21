import { Injectable, signal, Signal, WritableSignal } from '@angular/core';

@Injectable({
    providedIn: 'root',
})
export class NotificationDrawerService {
    private _switchTrigger: WritableSignal<boolean> = signal<boolean>(false);
    public readonly drawerState: Signal<boolean> = this._switchTrigger.asReadonly();

    public toggle(): void {
        this._switchTrigger.set(!this._switchTrigger());
    }
}
