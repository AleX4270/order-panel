import { CommonModule } from '@angular/common';
import { Component, ContentChild, input, InputSignal, TemplateRef } from '@angular/core';
import { CardComponent } from '../card/card.component';

@Component({
    selector: 'app-list-table',
    imports: [
        CommonModule,
        CardComponent
    ],
    template: `
        <div class="row">
            <app-card [isContentCentered]="true">
                <table class="w-100 p-0 my-4">
                    <thead>
                        <tr class="list-header">
                            <ng-container *ngTemplateOutlet="headers"></ng-container>
                        </tr>
                    </thead>
                    <tbody>
                        @if(defineTableRowsExternally()) {
                            @for (item of data(); track item) {
                                <ng-container *ngTemplateOutlet="rows; context: { $implicit: item, row: item }"></ng-container>
                            }
                        }
                        @else {
                            @for (item of data(); track item) {
                                <tr>
                                    <ng-container *ngTemplateOutlet="rows; context: { $implicit: item, row: item }"></ng-container>
                                </tr>
                            }
                        }
                    </tbody>
                </table>
            </app-card>
        </div>
    `,
    styles: [`
        .list-header {
            color: var(--order-list-header-text-color);
            background-color: var(--order-list-header-background-color);
            
            ::ng-deep th {
                font-weight: 500;
                padding: 1rem 0 1rem 0.75rem;
            }
        }   
    `]
})
export class ListTableComponent {
    @ContentChild('headers', { read: TemplateRef }) headers!: TemplateRef<any>;
    @ContentChild('rows', { read: TemplateRef }) rows!: TemplateRef<any>;

    public data = input<any[]>();
    public defineTableRowsExternally: InputSignal<boolean> = input<boolean>(false);
}
