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
import { UserNotificationService } from './shared/services/user-notification/user-notification.service';
import { NotificationDrawerComponent } from "./shared/components/notification-drawer/notification-drawer.component";

@Component({
    selector: 'app-root',
    imports: [
    RouterOutlet,
    ToastDisplayerComponent,
    NavbarComponent,
    LanguageSelectorComponent,
    PromptModalComponent,
    NotificationDrawerComponent
],
    template: `
        <main class="w-full">
            @if(isUserAuthenticated()) {
                <app-navbar class="sticky top-0 z-50"/>
            }
            <div class="w-full px-2 sm:px-6 lg:px-20 content-box" [class.pt-5]="isUserAuthenticated()">
                <app-toast-displayer/>
                <app-prompt-modal />
                <app-notification-drawer>
                    <router-outlet></router-outlet>
                    @if(!isUserAuthenticated()) {
                        <app-language-selector class="fixed bottom-0 left-0 w-full px-3 py-2" />
                    }
                </app-notification-drawer>
            </div>
        </main>
    `,
    styles: [`
        main {
            background-color: color-mix(in oklch, var(--color-neutral) 2%, transparent);
            // background-color: red;
            // min-height: 100dvh;
        }

        .content-box {
            // min-height: calc(100vh - var(--navbar-height, 0px));
        }
    `]
})
export class AppComponent implements OnInit {
    private readonly translate = inject(TranslateService);
    private readonly store = inject(Store);
    private readonly ngSelectConfig = inject(NgSelectConfig);
    private readonly userNotificationService = inject(UserNotificationService);

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

        effect(() => {
            const isAuthenticated = this.isUserAuthenticated();
            if(isAuthenticated) {
                this.userNotificationService.loadUnreadNotifications();
                this.userNotificationService.connectToChannel();
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
