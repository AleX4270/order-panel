import { Component, inject } from '@angular/core';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-welcome-header',
    imports: [
        TranslatePipe
    ],
    template: `
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-primary">{{"welcomeHeader.welcomeBack" | translate}}</h2>
                <p class="text-primary">Zaloguj się, aby kontynuować</p>
            </div>
        </div>
    `,
    styles: ``
})
export class WelcomeHeaderComponent {
    private readonly translate = inject(TranslateService);
}
