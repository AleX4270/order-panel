import { Component } from '@angular/core';
import { CardComponent } from '../card/card.component';
import { TranslatePipe } from '@ngx-translate/core';
import { MapComponent, MarkerComponent } from '@maplibre/ngx-maplibre-gl';
import { environment } from '../../../../environments/environment';

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
                [zoom]="[8]"
                [center]="[16.9252, 52.4064]"
                [canvasContextAttributes]="{preserveDrawingBuffer: true}"
                [cooperativeGestures]="true"
            >
                
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
}
