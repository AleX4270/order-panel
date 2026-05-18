import { ApplicationConfig, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';
import { routes } from './app.routes';
import { LanguageType } from './shared/enums/enums';
import { provideHttpClient, withInterceptors } from "@angular/common/http";
import { provideTranslateService } from "@ngx-translate/core";
import { provideTranslateHttpLoader } from '@ngx-translate/http-loader';
import { csrfInterceptor } from './shared/interceptors/csrf.interceptor';
import { provideStore } from '@ngxs/store';
import { withNgxsStoragePlugin } from '@ngxs/storage-plugin';
import { authErrorInterceptor } from './shared/interceptors/auth-error.interceptor';
import { UserState } from './shared/store/user/user.state';

export const appConfig: ApplicationConfig = {
    providers: [
        provideZoneChangeDetection({ eventCoalescing: true }),
        provideRouter(routes),
        provideHttpClient(withInterceptors([
            csrfInterceptor,
            authErrorInterceptor
        ])),
        provideTranslateService({
            loader: provideTranslateHttpLoader({ prefix: './i18n/', suffix: '.json' }),
            fallbackLang: LanguageType.english,
        }),
        provideStore([
            UserState
        ], withNgxsStoragePlugin({
            keys: ['user']
        })),
    ]
};
