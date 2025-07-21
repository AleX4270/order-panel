import { Component, inject, OnInit } from '@angular/core';
import { SmallFooterComponent } from "../small-footer/small-footer.component";
import { NavbarElementComponent } from "../navbar-element/navbar-element.component";
import { Store } from '@ngxs/store';
import { AuthState } from '../../store/auth/auth.state';
import { LocalDataService } from '../../services/local-data/local-data.service';
import { NavbarElement } from '../../types/navbar.types';

@Component({
    selector: 'app-navbar',
    imports: [SmallFooterComponent, NavbarElementComponent],
    template: `
        @if(isUserAuthenticated()) {
            <nav class="row">
                <div class="navbar d-flex align-items-center justify-content-center">
                    <div class="col-2 user-profile">
                        User profile
                    </div>

                    <div class="col-7 navbar-option d-flex gap-5">
                        @for(navElement of navbarElementList; track navElement) {
                            <app-navbar-element
                                [label]="navElement.label"
                                [url]="navElement.url"
                            />
                        }
                    </div>

                    <div class="col-1 language-selector">
                        PL
                    </div>

                    <div class="col-2 copyright-info">
                        <app-small-footer/>
                    </div>
                </div>
            </nav>
        }
    `,
    styles: [`
        .navbar {
            padding: 0 2% 0 2%;
            min-height: 55px;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.06) 0px 1px 2px 0px;
        }
    `]
})
export class NavbarComponent implements OnInit {
    private readonly store = inject(Store);
    private readonly localDataService = inject(LocalDataService);

    protected isUserAuthenticated = this.store.selectSignal(AuthState.isAuthenticated);
    protected navbarElementList: NavbarElement[] = [];

    ngOnInit(): void {
        this.localDataService.getNavbarData().subscribe({
            next: (res) => {
                this.navbarElementList = res;
                console.log(this.navbarElementList);
            },
            error: (err) => {
                console.error(err);
            }
        });
    }
}
