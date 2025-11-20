import { inject } from "@angular/core";
import { Store } from "@ngxs/store";
import { AuthService } from "../services/api/auth/auth.service";
import { catchError, Observable, of, tap } from "rxjs";
import { LoginUser, LogoutUser } from "../store/user/user.actions";
import { ApiResponse } from "../types/rest.types";
import { UserDetailsResponse } from "../types/auth.types";
import { LanguageType } from "../enums/enums";

export function initializeAuth(): Observable<ApiResponse<UserDetailsResponse> | null> {
    const store = inject(Store);
    const authService = inject(AuthService);

    return authService.user().pipe(
        tap(res => {
            if(res.data) {
                store.dispatch(new LoginUser({ username: res.data.name, isAuthenticated: true }));
            }
            else {
                store.dispatch(new LogoutUser());
            }
        }),
        catchError(() => {
            store.dispatch(new LogoutUser());
            return of(null);
        })
    );
}
