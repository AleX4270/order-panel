import { Component, effect, inject, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { LanguageType } from './shared/enums/enums';
import { ToastDisplayerComponent } from './shared/components/toast-displayer/toast-displayer.component';
import { NavbarComponent } from "./shared/components/navbar/navbar.component";
import { Store } from '@ngxs/store';
import { UserState } from './shared/store/user/user.state';
import { LanguageSelectorComponent } from "./shared/components/language-selector/language-selector.component";

@Component({
    selector: 'app-root',
    imports: [
    RouterOutlet,
    ToastDisplayerComponent,
    NavbarComponent,
    LanguageSelectorComponent
],
    template: `
        @if(isUserAuthenticated()) {
            <app-navbar class="sticky-top"/>
        }
        <main class="app-container container-fluid">
            <app-toast-displayer/>
            <router-outlet></router-outlet>
            @if(!isUserAuthenticated()) {
                <app-language-selector class="fixed-bottom px-3 py-2" [dropDirection]="'up'" />
            }
        </main>
    `,
    styles: [``]
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
    }
}
