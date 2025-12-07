import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';
import { TileType } from '../../enums/enums';
import { APP_VERSION } from '../../../app.constants';

@Component({
    selector: 'app-small-footer',
    imports: [
        TranslatePipe,
    ],
    template: `
        <div class="row text-center small text-secondary">
            <div class="col-12 d-flex justify-content-center align-items-center gap-2">
                <div><small>&copy; {{ currentYear + ' ' + ('basic.copyright' | translate) + ' | v' + appVersion}}</small></div>
            </div>
        </div>
    `,
    styles: [`
        small {
            font-weight: var(--font-weight-light);
        }    
    `]
})
export class SmallFooterComponent {
    protected currentYear!: number;
    protected tileType = TileType;
    protected appVersion = APP_VERSION;

    constructor() {
        this.currentYear = new Date().getFullYear();
    }
}
