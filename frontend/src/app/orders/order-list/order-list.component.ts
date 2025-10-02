import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';
import { ListTableComponent } from '../../shared/components/list-table/list-table.component';

@Component({
    selector: 'app-order-list',
    imports: [
        TranslatePipe,
        ListTableComponent
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
            <div class="col-12">
                <app-list-table
                    [data]="[
                        {
                            number: '1/2025',
                            address: 'Wiry 22/24',
                            priority: 'Normalny',
                            remarks: 'Proszę o kontakt przed przyjazdem',
                        },
                        {
                            number: '2/2025',
                            address: 'Nałęczów 22/24',
                            priority: 'Wysoki',
                            remarks: 'Proszę o nie dzwonienie',
                        }
                    ]"
                >
                    <ng-template #headers>
                        <tr>
                            <th>Numer zlecenia</th>
                            <th>Adres</th>
                            <th>Priorytet</th>
                            <th>Uwagi</th>
                        </tr>
                    </ng-template>

                    <ng-template #rows let-row>
                        <tr>
                            <td>{{ row.number }}</td>
                            <td>{{ row.address }}</td>
                            <td>{{ row.priority }}</td>
                            <td>{{ row.remarks }}</td>
                        </tr>
                    </ng-template>
                </app-list-table>
            </div>
        </div>

        <div class="row order-list-pagination">
            <div class="col-12">Paginacja</div>
        </div>
    `,
    styles: [``]
})
export class OrderListComponent {
    
}
