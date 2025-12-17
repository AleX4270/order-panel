import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';
import { APP_VERSION } from '../../../app.constants';

@Component({
    selector: 'app-small-footer',
    imports: [
        TranslatePipe,
    ],
    template: `
        <div class="flex justify-center items-center">
            <small class="text-neutral/50 text-xs">&copy; {{ currentYear + ' ' + ('basic.copyright' | translate) + ' | v' + appVersion}}</small>
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
    protected appVersion = APP_VERSION;

    constructor() {
        this.currentYear = new Date().getFullYear();
    }
}
