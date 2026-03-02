import { Component, inject } from '@angular/core';
import { SmallFooterComponent } from "../shared/components/small-footer/small-footer.component";
import { TranslatePipe } from '@ngx-translate/core';
import { Store } from '@ngxs/store';
import { UserState } from '../shared/store/user/user.state';

@Component({
    selector: 'app-not-found-page',
    imports: [
        SmallFooterComponent,
        TranslatePipe,
    ],
    template: `
        <div class="flex flex-col justify-center items-center gap-2 mt-10">
            <div class="card bg-base-100 max-w-lg p-2 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-error">{{"basic.notFound" | translate}}</h2>
                    <p class="text-base-content/60">{{"basic.notFoundDescription" | translate}}</p>
                </div>
            </div>
            @if(!isAuthenticated()) {
                <app-small-footer />
            }
        </div>
    `,
    styles: [``]
})
export class NotFoundPageComponent {
    private readonly store = inject(Store);
    protected readonly isAuthenticated = this.store.selectSignal(UserState.isAuthenticated);
}
