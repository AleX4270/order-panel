import { Component } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-welcome-header',
    imports: [
        TranslatePipe
    ],
    template: `
        <h1 class="text-primary font-bold text-3xl">{{"auth.header" | translate}}</h1>
    `,
    styles: ``
})
export class WelcomeHeaderComponent {}
