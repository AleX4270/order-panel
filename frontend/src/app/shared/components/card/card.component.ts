import { CommonModule } from '@angular/common';
import { Component, input } from '@angular/core';

@Component({
    selector: 'app-card',
    imports: [CommonModule],
    template: `
        <div 
            class="card card-border bg-base-100 w-full shadow-sm"
        >
            <div class="card-body">
                @if(title()) {
                    <h2 class="card-title text-md">{{ title() }}</h2>
                }
                <div
                    [class.flex]="isContentCentered()"
                    [class.items-center]="isContentCentered()"
                    [class.justify-center]="isContentCentered()"
                >
                    <ng-content/>
                </div>
            </div>
        </div>
    `,
    styles: [``],
})
export class CardComponent {
    public isContentCentered = input<boolean>(false);
    public title = input<string | null>(null);

    public overflowType = input<'hidden' | 'visible' | 'scroll'>('hidden');
    public color = input<string | null>(null);
}
