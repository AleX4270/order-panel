import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-welcome-header',
    imports: [
        TranslatePipe
    ],
    template: `
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-primary">{{"auth.header" | translate}}</h2>
            </div>
        </div>
    `,
    styles: ``
})
export class WelcomeHeaderComponent {}
