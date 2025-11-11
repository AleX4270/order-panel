import { Component, inject  } from '@angular/core';
import { Store } from '@ngxs/store';
import { AuthService } from '../shared/services/api/auth/auth.service';
import { Router } from '@angular/router';

@Component({
    selector: 'app-dashboard',
    imports: [],
    template: ``,
    styles: [`

    `]
})
export class DashboardComponent {
    private readonly authService = inject(AuthService);
    private readonly store = inject(Store);
    private readonly router = inject(Router);
}
