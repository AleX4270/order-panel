import { Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { PasswordRecoveryComponent } from './password-recovery/password-recovery.component';
import { NotFoundPageComponent } from './not-found-page/not-found-page.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { authGuard } from './shared/guards/auth.guard';
import { guestGuard } from './shared/guards/guest.guard';
import { OrderListComponent } from './orders/order-list/order-list.component';
import { UserListComponent } from './users/user-list/user-list.component';
import { Permission } from './shared/enums/permission.enum';

export const routes: Routes = [
    {
        path: '',
        component: LoginComponent,
        canActivate: [guestGuard],
    },
    {
        path: 'password-recovery',
        component: PasswordRecoveryComponent,
    },
    {
        path: 'dashboard',
        component: DashboardComponent,
        canActivate: [authGuard],
    },
    {
        path: 'orders',
        component: OrderListComponent,
        canActivate: [authGuard(Permission.orders_view)],
    },
    {
        path: 'users',
        component: UserListComponent,
        canActivate: [authGuard(Permission.users_view)],
    },
    {
        path: '**',
        component: NotFoundPageComponent,
    }
];
