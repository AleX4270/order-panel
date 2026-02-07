import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { switchMap } from 'rxjs';
import { UserLoginCredentials } from '../../../types/auth.types';
import { ApiResponse } from '../../../types/rest.types';
import { UserDetailsResponse } from '../../../types/auth.types';
import { RestService } from '../rest/rest.service';

@Injectable({
    providedIn: 'root'
})
export class AuthService extends RestService {
    public login(userCredentials: UserLoginCredentials): Observable<ApiResponse<UserDetailsResponse>> {
        return this.http.get<Response>(`${this.baseApiUrl}/sanctum/csrf-cookie`, { withCredentials: true }).pipe(
            switchMap(() => {
                return this.http.post<ApiResponse<UserDetailsResponse>>(`${this.apiUrl}/login`, userCredentials, { withCredentials: true });
            })
        );
    }

    public logout(): Observable<ApiResponse<void>> {
        return this.http.post<ApiResponse<void>>(`${this.apiUrl}/logout`, {});
    }

    public user(): Observable<ApiResponse<UserDetailsResponse>> {
        return this.http.get<ApiResponse<UserDetailsResponse>>(`${this.apiUrl}/user`);
    }
}
