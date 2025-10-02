import { CommonModule } from '@angular/common';
import { Component, ContentChild, input, TemplateRef } from '@angular/core';
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
                <table class="table p-0 my-4">
                    <thead>
                        <ng-container *ngTemplateOutlet="headers"></ng-container>
                    </thead>
                    <tbody>
                        @for (row of data(); track row) {
                            <ng-container *ngTemplateOutlet="rows; context: { $implicit: row, row: row }"></ng-container>
                        }
                    </tbody>
                </table>
            </app-card>
        </div>
    `,
    styles: [``]
})
export class ListTableComponent {
    @ContentChild('headers', { read: TemplateRef }) headers!: TemplateRef<any>;
    @ContentChild('rows', { read: TemplateRef }) rows!: TemplateRef<any>;

    public data = input<any[]>();
}
