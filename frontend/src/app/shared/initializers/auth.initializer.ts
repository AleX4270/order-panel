import { inject } from "@angular/core";
import { Store } from "@ngxs/store";
import { AuthService } from "../services/auth/auth.service";
import { catchError, Observable, of, tap } from "rxjs";
import { LoginUser, LogoutUser } from "../store/auth/auth.actions";
import { ApiResponse } from "../types/http.types";
import { UserDetailsResponse } from "../types/auth.types";

export function initializeAuth(): Observable<ApiResponse<UserDetailsResponse> | null> {
    const store = inject(Store);
    const authService = inject(AuthService);

    return authService.user().pipe(
        tap(res => {
            if(res.data) {
                store.dispatch(new LoginUser({ username: res.data.name }));
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
