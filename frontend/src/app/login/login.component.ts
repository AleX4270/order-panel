import { WelcomeHeaderComponent } from "../shared/components/welcome-header/welcome-header.component";
import { Component } from '@angular/core';
import { SmallFooterComponent } from "../shared/components/small-footer/small-footer.component";

@Component({
    selector: 'app-login',
    imports: [WelcomeHeaderComponent, SmallFooterComponent],
    template: `
        <div class="row">
            <div class="col-12 d-flex flex-column justify-content-center align-items-center">
                <app-welcome-header/>

                <div class="row w-25">
                    <div class="col-12" style="background-color: yellow;">
                        <div class="row">
                            <div class="col-12 text-center">
                                <h5>System User Login</h5>
                                <p>Enter your credentials to access the management panel</p>
                            </div>
                        </div>

                        <div class="row">
                            <form class="col-12">
                                <!-- TODO: Email -->
                                <!-- TODO: Password -->
                            </form>
                        </div>

                        <div class="row d-flex justify-content-center">
                            <button class="col-10 btn btn-primary fw-semibold">
                                Sign in
                            </button>
                        </div>

                        <hr>
                        <app-small-footer/>
                    </div>
                </div>    
            </div>
        </div>
    `,
    styles: ``
})
export class LoginComponent {

}
