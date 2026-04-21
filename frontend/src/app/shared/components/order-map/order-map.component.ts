import { Component, input, InputSignal, signal, WritableSignal } from '@angular/core';
import { MapComponent, MarkerComponent, PopupComponent } from '@maplibre/ngx-maplibre-gl';
import { CardComponent } from '../card/card.component';
import { OrderItem } from '../../types/order.types';
import { TranslatePipe } from '@ngx-translate/core';
import { Status } from '../../enums/status.enum';
import { DatePipe } from '@angular/common';

@Component({
    selector: 'app-order-map',
    imports: [
    MapComponent,
    CardComponent,
    MarkerComponent,
    TranslatePipe,
    PopupComponent,
    DatePipe,
],
    template: `
        <app-card overflowType="visible" [isFullHeight]="true" [isCollapsible]="true" [title]="'basic.map' | translate">
            <mgl-map
                [mapStyle]="
                    'https://tiles.openfreemap.org/styles/liberty'
                "
                [zoom]="[8]"
                [center]="[16.9252, 52.4064]"
                [canvasContextAttributes]="{preserveDrawingBuffer: true}"
                [cooperativeGestures]="true"
            >
                @for(order of orders(); track order.id) {
                    <mgl-marker #orderMarker [lngLat]="[order.coordinates.longitude, order.coordinates.latitude]" [scale]="0.8" [color]="getMarkerColorByStatus(order.statusSymbol, order.isOverdue)"></mgl-marker>

                    <mgl-popup [marker]="orderMarker">
                        <div>
                            <span class="font-medium">#{{order.id + ' | '}}</span>
                            <span>{{order.address + ', ' + order.cityName}}</span>
                        </div>

                        <div>
                            <span class="font-light">{{'orderMapMarker.priority' | translate}}:&nbsp;</span>
                            <span>{{order.priorityName}}</span>
                        </div>

                        <div>
                            <span class="font-light">{{'orderMapMarker.dateDeadline' | translate}}:&nbsp;</span>
                            <span>{{order.dateDeadline | date:'dd-MM-yyyy'}}</span>
                        </div>
                    </mgl-popup>
                }
            </mgl-map>
        </app-card>
    `,
    styles: [`
        mgl-map {
            width: 100%;
            height: 100%;
        }    
    `],
})
export class OrderMapComponent {
    public orders: InputSignal<OrderItem[]> = input<OrderItem[]>([]);

    protected getMarkerColorByStatus(status: Status, isOverdue: boolean): string {
        if(isOverdue && status !== Status.completed) {
            return 'var(--color-error)';
        }

        switch(status) {
            case Status.completed:
                return 'var(--color-neutral-100)';
            case Status.in_progress:
                return 'var(--color-primary)';
        }
    }
}
