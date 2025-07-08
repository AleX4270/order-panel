import { Component, inject } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { LanguageType } from './shared/enums/enums';

@Component({
    selector: 'app-root',
    imports: [RouterOutlet],
    template: `
        <main class="app-container container-fluid">
        <router-outlet></router-outlet>
        </main>
    `,
    styles: [`
        main {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8fafc, #e9f0f7);
        }
    `]
})
export class AppComponent {
    constructor(
        private readonly translate: TranslateService
    ) {
        this.translate.addLangs([
            LanguageType.polish,
            LanguageType.english,
        ]);
        this.translate.setDefaultLang(LanguageType.polish);
        this.translate.use(LanguageType.polish);
    }
}
