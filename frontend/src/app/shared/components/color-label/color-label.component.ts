import { CommonModule } from '@angular/common';
import { Component, input, InputSignal } from '@angular/core';

@Component({
    selector: 'app-color-label',
    imports: [CommonModule],
    template: `
        <div class="flex items-center gap-1">
            <div class="w-3 h-3 rounded-sm border border-black/30" [ngStyle]="color() ? {'background-color': color()} : {}" [ngClass]="colorClass()"></div>
            <span class="text-xs">{{label()}}</span>
        </div>
    `,
    styles: [``],
})
export class ColorLabelComponent {
    public colorClass: InputSignal<string> = input<string>('');
    public color: InputSignal<string> = input<string>('');
    public label: InputSignal<string> = input.required<string>();
}
