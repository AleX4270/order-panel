import { Component } from '@angular/core';
import { SmallFooterComponent } from "../shared/components/small-footer/small-footer.component";
import { TranslatePipe } from '@ngx-translate/core';

@Component({
    selector: 'app-not-found-page',
    imports: [
        SmallFooterComponent,
        TranslatePipe,
    ],
    template: `
        <div class="row vh-100 d-flex justify-content-center align-items-center">
            <div class="col-3">
                <div class="resource-not-found-card shadow-sm p-4 mt-3 mb-3 bg-white">
                    <div class="resource-not-found-card-header">
                        <h2 class="text-danger">{{"basic.notFound" | translate}}</h2>
                        <p class="text-muted mt-2">{{"basic.notFoundDescription" | translate}}</p>
                    </div>
                </div>
                <app-small-footer />
            </div>
        </div>
    `,
    styles: [`
        .resource-not-found-card {
            border: 1px solid var(--resource-not-found-card-background-color);
            border-radius: var(--radius-lg);
        }
    `]
})
export class NotFoundPageComponent {}
