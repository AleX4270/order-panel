import { CommonModule } from '@angular/common';
import { Component, ContentChild, input, InputSignal, TemplateRef } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-list-table',
    imports: [
        CommonModule,
        TranslatePipe,
    ],
    template: `
        <div class="w-full overflow-x-auto rounded-box bg-base-100 shadow-sm">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-100 text-base-content [&_th]:font-light [&_th]:text-xs">
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
                                    <div class="flex justify-center items-center pt-3">
                                        <small class="text-base-content/50">{{'listTable.noRecordsFound' | translate}}</small>
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
                                    <div class="flex justify-center items-center pt-3">
                                        <small class="text-base-content/50">{{'listTable.noRecordsFound' | translate}}</small>
                                    </div>
                                </td>
                            </tr>
                        }
                    }
                </tbody>
            </table>
        </div>
    `,
    styles: []
})
export class ListTableComponent {
    @ContentChild('headers', { read: TemplateRef }) headers!: TemplateRef<any>;
    @ContentChild('rows', { read: TemplateRef }) rows!: TemplateRef<any>;

    public data = input<any[]>();
    public defineTableRowsExternally: InputSignal<boolean> = input<boolean>(false);
}
