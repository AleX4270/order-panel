import { Component, input, InputSignal } from '@angular/core';
import { MapComponent, MarkerComponent, PopupComponent } from '@maplibre/ngx-maplibre-gl';
import { CardComponent } from '../card/card.component';
import { OrderItem } from '../../types/order.types';
import { TranslatePipe } from '@ngx-translate/core';
import { Status } from '../../enums/status.enum';
import { DatePipe } from '@angular/common';
import { environment } from '../../../../environments/environment';
import { Coordinates } from '../../types/address.types';
import { DEFAULT_COORDINATES } from '../../constants/map.const';

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
        <app-card overflowType="visible" [isFullHeight]="true" [isCollapsible]="false" [title]="'basic.map' | translate">
            <mgl-map
                [mapStyle]="environment.map.tileProviderUrl"
                [zoom]="[7]"
                [center]="[coordinates().longitude, coordinates().latitude]"
                [canvasContextAttributes]="{preserveDrawingBuffer: true}"
                [cooperativeGestures]="true"
                movingMethod="jumpTo"
            >
                @for(order of orders(); track order) {
                    <mgl-marker #orderMarker [lngLat]="[order.coordinates.longitude, order.coordinates.latitude]" [scale]="0.8" [color]="getMarkerColorByStatus(order.statusSymbol, order.isOverdue)"></mgl-marker>

                    <mgl-popup [marker]="orderMarker">
                        <div>
                            <span class="font-medium">#{{order.id + ' | '}}</span>
                            <span>{{order.address + ', ' + order.cityName}}</span>
                        </div>

                        <div>
                            <span class="font-light">{{'orderMapMarker.distance' | translate}}:&nbsp;</span>
                            <span>{{order.distance}} km</span>
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
export class OrderMapComponent {
    protected readonly environment = environment;

    public orders: InputSignal<OrderItem[]> = input<OrderItem[]>([]);
    public coordinates = input<Coordinates>(DEFAULT_COORDINATES);

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
