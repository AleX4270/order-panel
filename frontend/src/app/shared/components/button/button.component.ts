import { Component, input, InputSignal, output } from '@angular/core';

type ButtonType = 'submit' | 'button';

@Component({
    selector: 'app-button',
    imports: [],
    template: `
        <button [type]="type()" [disabled]="isDisabled()" [class]="classList()" (click)="onClick()">
            @if(isLoading()) {
                <span class="loading loading-spinner"></span>
            }
            @else {
                <span>{{ label() }}</span>
            }
        </button>
    `,
    styles: [``]
})
export class ButtonComponent {
    public type: InputSignal<ButtonType> = input<ButtonType>('button');
    public isLoading: InputSignal<boolean> = input<boolean>(false);
    public isDisabled: InputSignal<boolean> = input<boolean>(false);
    public label: InputSignal<string | null> = input<string | null>(null);
    public classList: InputSignal<string | null> = input<string | null>(null);

    public click = output<void>();

    protected onClick(): void {
        this.click.emit();
    }
}
