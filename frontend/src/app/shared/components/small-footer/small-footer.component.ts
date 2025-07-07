import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-small-footer',
    imports: [
        TranslatePipe
    ],
    template: `
        <div class="row text-center mt-3 small text-secondary">
            <div class="col-12">
                <p>&copy; {{ currentYear + ' ' + ('basic.copyright' | translate)}}</p>
            </div>
        </div>
    `,
    styles: ``
})
export class SmallFooterComponent {
    protected currentYear!: number;

    constructor() {
        this.currentYear = new Date().getFullYear();
    }
}
