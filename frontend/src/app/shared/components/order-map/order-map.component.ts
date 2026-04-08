import { Component } from '@angular/core';
import { ControlComponent, MapComponent } from '@maplibre/ngx-maplibre-gl';
import { CardComponent } from '../card/card.component';

@Component({
    selector: 'app-order-map',
    imports: [
        MapComponent,
        ControlComponent,
        CardComponent,
    ],
    template: `
        <app-card overflowType="visible" [isFullHeight]="true">
            <mgl-map
                [mapStyle]="
                    'https://tiles.openfreemap.org/styles/liberty'
                "
                [zoom]="[9]"
                [center]="[16.9252, 52.4064]"
                [canvasContextAttributes]="{preserveDrawingBuffer: true}"
            />
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

}
