import { Component, inject, input } from '@angular/core';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-navbar-element',
    imports: [TranslatePipe],
    template: `
        <div class="navbar-element">
            {{"navbar." + label() | translate}}
        </div>
    `,
    styles: [`
        .navbar-element {
            font-weight: 500;
            color: var(--navbar-element-text);
            padding: 0.5rem 0.75rem 0.5rem 0.75rem;
            border-radius: var(--navbar-element-radius);
            transition: color 200ms, background-color 200ms;
            cursor: pointer;
            border: none;
            background-color: transparent;

            &:hover {
                color: var(--navbar-element-text-active);
                background-color: var(--navbar-element-background-hover);
            }

            &.active {
                color: var(--navbar-element-text-active);
                background-color: var(--navbar-element-background-active);

                &:hover {
                    color: var(--navbar-element-text-active);
                    background-color: var(--navbar-element-background-active);
                }
            }
        }
    `]
})
export class NavbarElementComponent {
    public label = input.required();
    public url = input.required();
}
