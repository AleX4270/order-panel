import { CommonModule } from '@angular/common';
import { Component, inject, input } from '@angular/core';
import { Router } from '@angular/router';
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-navbar-element',
    imports: [TranslatePipe, CommonModule],
    template: `
        <div class="navbar-element">
            <button class="btn bg-transparent border-0 p-0 m-0" (click)="onButtonClick()">
                {{"navbar." + label() | translate}}
            </button>
        </div>
    `,
    styles: [``]
})
export class NavbarElementComponent {
    private router = inject(Router);

    public label = input.required();
    public url = input.required();

    protected onButtonClick(): void {
        this.router.navigate([this.url()]);
    }
}
