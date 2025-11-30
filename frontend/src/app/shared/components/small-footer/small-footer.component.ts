import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-small-footer',
    imports: [
        TranslatePipe
    ],
    template: `
        <div class="row text-center small text-secondary">
            <div class="col-12">
                <span>&copy; {{ currentYear + ' ' + ('basic.copyright' | translate)}}</span>
            </div>
        </div>
    `,
    styles: [``]
})
export class SmallFooterComponent {
    protected currentYear!: number;

    constructor() {
        this.currentYear = new Date().getFullYear();
    }
}
