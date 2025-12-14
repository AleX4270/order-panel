import { Component, effect, inject, signal, input } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';
import { Store } from '@ngxs/store';
import { SetUserLanguage } from '../../store/user/user.actions';
import { LanguageType } from '../../enums/enums';
import { UserState } from '../../store/user/user.state';
import { DropdownDirection } from '../../types/common.types';
import { CommonModule } from '@angular/common';
import { DropdownComponent } from "../dropdown/dropdown.component";

@Component({
  selector: 'app-language-selector',
  imports: [
    CommonModule,
    DropdownComponent
],
  template: `
    <app-dropdown classList="dropdown-top">
        <ng-template #label><span class="text-primary uppercase">{{ currentLanguage() }}</span></ng-template>
        <ng-template #options>
            @for(language of languageList(); track language) {
                <li><button class="uppercase text-primary" type="button" (click)="setLanguage(language)">{{language}}</button></li>
            }
        </ng-template>
    </app-dropdown>
  `,
  styles: [``]
})
export class LanguageSelectorComponent {
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
