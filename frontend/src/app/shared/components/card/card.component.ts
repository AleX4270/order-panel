import { Component, input } from '@angular/core';

@Component({
    selector: 'app-card',
    imports: [],
    template: `
        <div 
            class="card-body"
            [class.d-flex]="isContentCentered()"
            [class.align-items-center]="isContentCentered()"
            [class.justify-content-center]="isContentCentered()"
        >
            <ng-content/>
        </div>
    `,
    styles: [`
        .card-body {
            background-color: var(--card-background-color);
            border: 1px solid var(--card-border-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            padding: 0;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }
    `],
})
export class CardComponent {
    public isContentCentered = input<boolean>(false);
}
