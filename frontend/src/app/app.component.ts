import { Component, effect, inject, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { LanguageType } from './shared/enums/enums';
import { ToastDisplayerComponent } from './shared/components/toast-displayer/toast-displayer.component';
import { NavbarComponent } from "./shared/components/navbar/navbar.component";
import { Store } from '@ngxs/store';
import { UserState } from './shared/store/user/user.state';
import { LanguageSelectorComponent } from "./shared/components/language-selector/language-selector.component";
import { NgSelectConfig } from '@ng-select/ng-select';
import { PromptModalComponent } from './shared/components/prompt-modal/prompt-modal.component';

@Component({
    selector: 'app-root',
    imports: [
        RouterOutlet,
        ToastDisplayerComponent,
        NavbarComponent,
        LanguageSelectorComponent,
        PromptModalComponent,
    ],
    template: `
        @if(isUserAuthenticated()) {
            <app-navbar class="sticky top-0 z-50"/>
        }
        <main class="w-full px-2 sm:px-6 lg:px-20" [class.pt-7]="isUserAuthenticated()">
            <app-toast-displayer/>
            <app-prompt-modal />
            <router-outlet></router-outlet>
            @if(!isUserAuthenticated()) {
                <app-language-selector class="fixed bottom-0 left-0 w-full px-3 py-2" />
            }
        </main>
    `,
    styles: [``]
})
export class AppComponent implements OnInit {
    private readonly translate = inject(TranslateService);
    private readonly store = inject(Store);
    private readonly ngSelectConfig = inject(NgSelectConfig);

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

        this.translate.get('basic.noResults').subscribe({
            next: (result) => {
                this.ngSelectConfig.notFoundText = result;
            }
        })
    }
}
