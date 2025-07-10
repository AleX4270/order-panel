import { Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { PasswordRecoveryComponent } from './password-recovery/password-recovery.component';
import { NotFoundPageComponent } from './not-found-page/not-found-page.component';

export const routes: Routes = [
    {
        path: '',
        component: LoginComponent,
    },
    {
        path: 'password-recovery',
        component: PasswordRecoveryComponent,
    },
    {
        path: '**',
        component: NotFoundPageComponent,
    }
];
