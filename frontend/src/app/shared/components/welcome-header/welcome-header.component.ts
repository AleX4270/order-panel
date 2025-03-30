import { Component } from '@angular/core';

@Component({
    selector: 'app-welcome-header',
    imports: [],
    template: `
        <div class="row">
            <div class="col-12 text-center">
                <!-- TODO: Icon -->
                <h3>Order Management System</h3>
                <h6>Administrative portal for order processing and inventory management</h6>
            </div>
        </div>
    `,
    styles: ``
})
export class WelcomeHeaderComponent {

}
