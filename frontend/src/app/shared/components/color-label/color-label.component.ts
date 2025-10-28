import { CommonModule } from '@angular/common';
import { Component, input, InputSignal } from '@angular/core';

@Component({
    selector: 'app-color-label',
    imports: [CommonModule],
    template: `
        <div class="color-label-container d-flex align-items-center gap-1">
            <div class="color-label-box" [ngStyle]="{'background-color': color()}"></div>
            <span class="color-label">{{label()}}</span>
        </div>
    `,
    styles: [`
        $box-width: 14px;

        .color-label-box {
            width: $box-width;
            height: $box-width;
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--border-color);
        }

        .color-label {
            font-size: 0.8rem;
        }
    `],
})
export class ColorLabelComponent {
    public color: InputSignal<string> = input.required<string>();
    public label: InputSignal<string> = input.required<string>();
}
