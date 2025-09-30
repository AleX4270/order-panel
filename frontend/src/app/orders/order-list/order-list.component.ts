import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-order-list',
    imports: [
        TranslatePipe
    ],
    template: `
        <div class="row order-list-header">
            <div class="col-12">
                <div class="row">
                    <div class="col-6">
                        <h3>{{'orderList.header' | translate}}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">Filtry</div>
                </div>
            </div> 
        </div>

        <div class="row order-list-info">
            <div class="col-8">Legenda</div>
            <div class="col-4 text-end">Przycisk dodaj zlecenie</div>
        </div>

        <div class="row order-list-table">
            <div class="col-12">Lista</div>
        </div>

        <div class="row order-list-pagination">
            <div class="col-12">Paginacja</div>
        </div>
    `,
    styles: [``]
})
export class OrderListComponent {
    
}
