import { CommonModule } from '@angular/common';
import { Component, ContentChild, input, InputSignal, TemplateRef } from '@angular/core';

@Component({
    selector: 'app-dropdown',
    imports: [
        CommonModule,
    ],
    template: `
        <div class="dropdown" [ngClass]="classList()">
            <div tabindex="0" role="button" class="btn mt-1">{{label()}}</div>
            <ul 
                tabindex="-1" 
                class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm"
                (click)="blurActive()"
            >
                <ng-container *ngTemplateOutlet="options"></ng-container>
            </ul>
        </div>
    `,
    styles: []
})
export class DropdownComponent {
    @ContentChild('options', { read: TemplateRef }) options!: TemplateRef<any>;

    public classList: InputSignal<string | null> = input<string | null>(null);
    public label: InputSignal<string | null> = input<string | null>(null);

    protected blurActive(): void {
        (document.activeElement as HTMLElement | null)?.blur();
    }
}
