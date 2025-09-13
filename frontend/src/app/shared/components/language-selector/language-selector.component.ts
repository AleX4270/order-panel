import { Component, effect, inject, signal, input } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';
import { Store } from '@ngxs/store';
import { SetUserLanguage } from '../../store/user/user.actions';
import { LanguageType } from '../../enums/enums';
import { UserState } from '../../store/user/user.state';
import { DropdownDirection } from '../../types/common.types';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-language-selector',
  imports: [
    CommonModule,
  ],
  template: `
    <div 
        [ngClass]="{
            'dropup': dropDirection() === 'up',
            'dropdown': dropDirection() === 'down',
            'dropend': dropDirection() === 'end',
            'dropstart': dropDirection() === 'start',
        }"
    >
        <button class="btn bg-transparent border border-1 border-primary-subtle text-primary p-2 text-uppercase dropdown-toggle" data-bs-toggle="dropdown">{{currentLanguage()}}</button>
        <ul class="dropdown-menu mt-1">
            @for(language of languageList(); track language) {
                <li><button class="dropdown-item text-uppercase text-primary" type="button" (click)="setLanguage(language)">{{language}}</button></li>
            }
        </ul>
    </div>
  `,
  styles: [``]
})
export class LanguageSelectorComponent {
    public dropDirection = input<DropdownDirection>('down');

    private readonly translate = inject(TranslateService);
    private readonly store = inject(Store);

    protected languageList = signal<string[]>([]);
    protected currentLanguage = this.store.selectSignal(UserState.userLanguage);

    constructor() {
        effect(() => {
            this.languageList.set(this.translate.getLangs());
        });
    }

    protected setLanguage(languageSymbol: string): void {
        this.store.dispatch(new SetUserLanguage(languageSymbol as LanguageType));
    }
}
