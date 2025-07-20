import { Component, inject, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { LanguageType } from './shared/enums/enums';
import { ToastDisplayerComponent } from './shared/components/toast-displayer/toast-displayer.component';

@Component({
    selector: 'app-root',
    imports: [
        RouterOutlet,
        ToastDisplayerComponent,
    ],
    template: `
        <main class="app-container container-fluid">
            <app-toast-displayer/>
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
export class AppComponent implements OnInit {
    private readonly translate = inject(TranslateService);

    ngOnInit(): void {
        this.translate.addLangs([
            LanguageType.polish,
            LanguageType.english,
        ]);
        this.translate.setDefaultLang(LanguageType.polish);
        this.translate.use(LanguageType.polish);
    }
}
