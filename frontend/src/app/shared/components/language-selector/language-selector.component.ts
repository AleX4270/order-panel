import { Component, effect, inject, signal } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';
import { Store } from '@ngxs/store';
import { SetUserLanguage } from '../../store/user/user.actions';
import { LanguageType } from '../../enums/enums';
import { UserState } from '../../store/user/user.state';

@Component({
  selector: 'app-language-selector',
  imports: [],
  template: `
    <div class="dropdown">
        <div class="dropdown-toggle" data-bs-toggle="dropdown">
            <button class="btn bg-transparent border-0 p-0 text-uppercase">{{currentLanguage()}}</button>
        </div>
        <ul class="dropdown-menu mt-1">
            @for(language of languageList(); track language) {
                <li><button class="dropdown-item text-uppercase" type="button" (click)="setLanguage(language)">{{language}}</button></li>
            }
        </ul>
    </div>
  `,
  styles: [``]
})
export class LanguageSelectorComponent {
    private readonly translate = inject(TranslateService);
    private readonly store = inject(Store);

    protected languageList = signal<string[]>([]);
    protected currentLanguage = this.store.selectSignal(UserState.userLanguage);

    constructor() {
        console.log(this.translate.getLangs());
        effect(() => {
            this.languageList.set(this.translate.getLangs());
        });
    }

    protected setLanguage(languageSymbol: string): void {
        this.store.dispatch(new SetUserLanguage(languageSymbol as LanguageType));
        this.translate.use(languageSymbol);
    }
}
