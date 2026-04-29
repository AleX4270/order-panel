import { Component, input } from '@angular/core';
import { CardComponent } from '../card/card.component';
import { TranslatePipe } from '@ngx-translate/core';
import { MapComponent, MarkerComponent } from '@maplibre/ngx-maplibre-gl';
import { environment } from '../../../../environments/environment';
import { Coordinates } from '../../types/address.types';

@Component({
    selector: 'app-company-headquarters-map',
    imports: [
        CardComponent,
        TranslatePipe,
        MapComponent,
        MarkerComponent
    ],
    template: `
        <app-card overflowType="visible" [isFullHeight]="true" [isCollapsible]="false" [title]="'companySettings.headquartersView' | translate">
            <mgl-map
                [mapStyle]="environment.map.tileProviderUrl"
                [zoom]="[10]"
                [center]="[coordinates().longitude, coordinates().latitude]"
                [canvasContextAttributes]="{preserveDrawingBuffer: true}"
                [cooperativeGestures]="true"
            >
                <mgl-marker #orderMarker [lngLat]="[coordinates().longitude, coordinates().latitude]" [scale]="0.8"></mgl-marker>
            </mgl-map>
        </app-card>
    `,
    styles: [`
        mgl-map {
            width: 100%;
            height: 100%;
        }
    `]
})
export class CompanyHeadquartersMapComponent {
    protected readonly environment = environment;

    public coordinates = input<Coordinates>({longitude: 16.9252, latitude: 52.4064});
}
