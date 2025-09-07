import { Component, inject, OnInit } from '@angular/core';
import { SmallFooterComponent } from "../small-footer/small-footer.component";
import { NavbarElementComponent } from "../navbar-element/navbar-element.component";
import { Store } from '@ngxs/store';
import { UserState } from '../../store/user/user.state';
import { LocalDataService } from '../../services/local-data/local-data.service';
import { NavbarElement } from '../../types/navbar.types';
import { UserProfileNavbarComponent } from '../user-image/user-profile-navbar.component';
import { LanguageSelectorComponent } from '../language-selector/language-selector.component';

@Component({
    selector: 'app-navbar',
    imports: [SmallFooterComponent, NavbarElementComponent, UserProfileNavbarComponent, LanguageSelectorComponent],
    template: `
        <nav class="navbar navbar-expand-lg m-0 p-0">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse align-items-center justify-content-lg-between" id="navbarSupportedContent">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        @if(isUserAuthenticated()) {
                            <li class="nav-item me-4">
                                <app-user-profile-navbar />
                            </li>

                            @for(navElement of navbarElementList; track navElement) {
                                <li class="nav-item">
                                    <app-navbar-element
                                        [label]="navElement.label"
                                        [url]="navElement.url"
                                    />
                                </li>
                            }
                        }
                        @else {
                            <li class="navbar-brand me-4">
                                OrderPanel
                            </li>
                        }
                    </ul>

                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item me-3">
                            <app-language-selector/>
                        </li>

                        @if(isUserAuthenticated()) {
                            <li>
                                <span class="navbar-text">
                                    <app-small-footer/>
                                </span>
                            </li>
                        }
                    </ul>
                </div>
            </div>
        </nav>
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

    protected isUserAuthenticated = this.store.selectSignal(UserState.isAuthenticated); //TODO: Maybe this won't be needed
    protected navbarElementList: NavbarElement[] = [];

    ngOnInit(): void {
        this.localDataService.getNavbarData().subscribe({
            next: (res) => {
                this.navbarElementList = res;
            },
            error: (err) => {
                console.error(err);
            }
        });
    }
}
