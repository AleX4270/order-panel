import { Component, effect, inject, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { LanguageType } from './shared/enums/enums';
import { ToastDisplayerComponent } from './shared/components/toast-displayer/toast-displayer.component';
import { NavbarComponent } from "./shared/components/navbar/navbar.component";
import { Store } from '@ngxs/store';
import { UserState } from './shared/store/user/user.state';

@Component({
    selector: 'app-root',
    imports: [
    RouterOutlet,
    ToastDisplayerComponent,
    NavbarComponent
],
    template: `
        <main class="app-container container-fluid">
            <app-navbar/>
            <app-toast-displayer/>
            <router-outlet></router-outlet>
        </main>
    `,
    styles: [`
        main {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            //background: linear-gradient(135deg, #f8fafc, #e9f0f7);
        }
    `]
})
export class AppComponent implements OnInit {
    private readonly translate = inject(TranslateService);
    private readonly store = inject(Store);

    protected isUserAuthenticated = this.store.selectSignal(UserState.isAuthenticated);
    protected selectedLanguage = this.store.selectSignal(UserState.userLanguage);

    constructor() {
        effect(() => {
            if(this.selectedLanguage()) {
                this.translate.use(this.selectedLanguage() as string);
            }
            else {
                this.translate.use(LanguageType.polish);
            }
        });
    }

    ngOnInit(): void {
        this.translate.addLangs([
            LanguageType.polish,
            LanguageType.english,
        ]);
        this.translate.setDefaultLang(LanguageType.polish);

        if(this.selectedLanguage()) {
            this.translate.use(this.selectedLanguage() as string);
        }
        else {
            this.translate.use(LanguageType.polish);
        }
    }
}
