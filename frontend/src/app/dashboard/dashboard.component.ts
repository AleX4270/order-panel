import { Component, inject  } from '@angular/core';
import { Store } from '@ngxs/store';
import { AuthService } from '../shared/services/auth/auth.service';
import { LogoutUser } from '../shared/store/auth/auth.actions';
import { Router } from '@angular/router';

@Component({
    selector: 'app-dashboard',
    imports: [],
    template: `
        <h2>Dashboard works! Nice</h2>
        <button (click)="logout()" class="btn btn-danger">Wyloguj</button>
    `,
    styles: [`

    `]
})
export class DashboardComponent {
    private readonly authService = inject(AuthService);
    private readonly store = inject(Store);
    private readonly router = inject(Router);


    logout() {
        this.authService.logout().subscribe({
            next: (res: any) => {
                this.store.dispatch(new LogoutUser());
                this.router.navigate(['/']);
            }
        });
    }
}
