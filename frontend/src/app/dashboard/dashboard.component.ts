import { Component, inject  } from '@angular/core';
import { AuthService } from '../shared/services/auth/auth.service';

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
    constructor(private readonly authService: AuthService) {
        console.log('im here!');
    }

    logout() {
        this.authService.logout().subscribe({});
    }
}
