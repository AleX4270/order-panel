import { CommonModule } from '@angular/common';
import { Component, computed, input } from '@angular/core';

@Component({
    selector: 'app-card',
    imports: [CommonModule],
    template: `
        <div 
            class="card-container"
            [class.d-flex]="isContentCentered()"
            [class.align-items-center]="isContentCentered()"
            [class.justify-content-center]="isContentCentered()"
            [ngStyle]="{
                'background-color': color() ?? 'var(--card-background-color)',
                'overflow': overflowType(),
            }"
        >
            <ng-content/>
        </div>
    `,
    styles: [`
        .card-container {
            background-color: var(--card-background-color);
            border: 1px solid var(--card-border-color);
            border-radius: var(--border-radius);
            padding: 0;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
    `],
})
export class CardComponent {
    public isContentCentered = input<boolean>(false);
    public overflowType = input<'hidden' | 'visible' | 'scroll'>('hidden');
    public color = input<string | null>(null);
}
