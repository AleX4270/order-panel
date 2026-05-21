import { Component, input, InputSignal } from '@angular/core';
import { MapComponent, MarkerComponent, PopupComponent } from '@maplibre/ngx-maplibre-gl';
import { CardComponent } from '../card/card.component';
import { OrderRequestItem } from '../../types/order-request.types';
import { TranslatePipe } from '@ngx-translate/core';
import { DatePipe } from '@angular/common';
import { environment } from '../../../../environments/environment';
import { Coordinates } from '../../types/address.types';
import { DEFAULT_COORDINATES } from '../../constants/map.const';

@Component({
    selector: 'app-order-request-map',
    imports: [
        MapComponent,
        CardComponent,
        MarkerComponent,
        TranslatePipe,
        PopupComponent,
        DatePipe,
    ],
    template: `
        <app-card overflowType="visible" [isFullHeight]="true" [isCollapsible]="false" [title]="'basic.map' | translate">
            <mgl-map
                [mapStyle]="environment.map.tileProviderUrl"
                [zoom]="[7]"
                [center]="[coordinates().longitude, coordinates().latitude]"
                [canvasContextAttributes]="{preserveDrawingBuffer: true}"
                [cooperativeGestures]="true"
                movingMethod="jumpTo"
            >
                @for(orderRequest of orderRequests(); track orderRequest) {
                    <mgl-marker #orderRequestMarker [lngLat]="[orderRequest.coordinates.longitude, orderRequest.coordinates.latitude]" [scale]="0.8" [color]="'var(--color-primary)'"></mgl-marker>

                    <mgl-popup [marker]="orderRequestMarker">
                        <div>
                            <span class="font-medium">#{{orderRequest.id + ' | '}}</span>
                            <span>{{orderRequest.firstName + ' ' + orderRequest.lastName}}</span>
                        </div>

                        <div>
                            <span>{{orderRequest.address + ', ' + orderRequest.cityName}}</span>
                        </div>

                        <div>
                            <span class="font-light">{{'orderMapMarker.distance' | translate}}:&nbsp;</span>
                            <span>{{orderRequest.distance}} km</span>
                        </div>

                        <div>
                            <span class="font-light">{{'orderRequestMapMarker.dateCreated' | translate}}:&nbsp;</span>
                            <span>{{orderRequest.dateCreated | date:'dd-MM-yyyy'}}</span>
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
            isolation: isolate;
        }
    `],
})
export class OrderRequestMapComponent {
    protected readonly environment = environment;

    public orderRequests: InputSignal<OrderRequestItem[]> = input<OrderRequestItem[]>([]);
    public coordinates = input<Coordinates>(DEFAULT_COORDINATES);
}
