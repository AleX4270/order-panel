import { ApplicationConfig, provideZoneChangeDetection } from '@angular/core';
import { provideRouter } from '@angular/router';
import { routes } from './app.routes';
import { LanguageType } from './shared/enums/enums';
import { provideHttpClient, withInterceptors } from "@angular/common/http";
import { provideTranslateService, TranslateLoader } from "@ngx-translate/core";
import { TranslateHttpLoader } from '@ngx-translate/http-loader';
import { HttpClient } from '@angular/common/http';
import { csrfInterceptor } from './shared/interceptors/csrf.interceptor';
import { provideStore } from '@ngxs/store';
import { AuthState } from './shared/store/auth/auth.state';
import { withNgxsStoragePlugin } from '@ngxs/storage-plugin';

const httpLoaderFactory: (http: HttpClient) => TranslateHttpLoader = (http: HttpClient) =>
    new TranslateHttpLoader(http, './i18n/', '.json');

export const appConfig: ApplicationConfig = {
    providers: [
        provideZoneChangeDetection({ eventCoalescing: true }),
        provideRouter(routes),
        provideTranslateService({
            defaultLanguage: LanguageType.english
        }),
        provideHttpClient(withInterceptors([
            csrfInterceptor
        ])),
        provideTranslateService({
            loader: {
                provide: TranslateLoader,
                useFactory: httpLoaderFactory,
                deps: [HttpClient],
            },
        }),
        provideStore([
            AuthState
        ], withNgxsStoragePlugin({
            keys: ['auth']
        })),
    ]
};
