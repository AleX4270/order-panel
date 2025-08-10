import { Component, inject, input } from '@angular/core';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-navbar-element',
    imports: [TranslatePipe],
    template: `
        <div class="navbar-element">
            <button class="btn bg-transparent border-0 p-0 m-0">
                {{"navbar." + label() | translate}}
            </button>
        </div>
    `,
    styles: [`

    `]
})
export class NavbarElementComponent {
    public label = input.required();
    public url = input.required();
}
