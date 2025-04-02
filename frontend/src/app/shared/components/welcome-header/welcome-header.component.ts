import { Component } from '@angular/core';

@Component({
    selector: 'app-welcome-header',
    imports: [],
    template: `
        <div class="row">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-primary">Welcome back</h2>
                <p class="text-primary">Sign in to your account to continue</p>
            </div>
        </div>
    `,
    styles: ``
})
export class WelcomeHeaderComponent {

}
