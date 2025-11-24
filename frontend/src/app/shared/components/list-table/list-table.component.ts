import { CommonModule } from '@angular/common';
import { Component, ContentChild, input, InputSignal, TemplateRef } from '@angular/core';
import { CardComponent } from '../card/card.component';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-list-table',
    imports: [
        CommonModule,
        CardComponent,
        TranslatePipe,
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
                            <!-- TODO: Track by id -->
                            @for (item of data(); track item) {
                                <ng-container *ngTemplateOutlet="rows; context: { $implicit: item, row: item }"></ng-container>
                            }
                            @empty {
                                <tr>
                                    <td colspan="100%">
                                        <div class="d-flex justify-content-center align-items-center pt-3">
                                            <small class="text-muted">{{'listTable.noRecordsFound' | translate}}</small>
                                        </div>
                                    </td>
                                </tr>
                            }
                        }
                        @else {
                            @for (item of data(); track item) {
                                <tr>
                                    <ng-container *ngTemplateOutlet="rows; context: { $implicit: item, row: item }"></ng-container>
                                </tr>
                            }
                            @empty {
                                <tr>
                                    <td colspan="100%">
                                        <div class="d-flex justify-content-center align-items-center pt-3">
                                            <small class="text-muted">{{'listTable.noRecordsFound' | translate}}</small>
                                        </div>
                                    </td>
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
                font-weight: var(--font-weight-medium);
                padding: 0.8rem 0 0.8rem 0.75rem;
                font-size: var(--font-size-sm);
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
