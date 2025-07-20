import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { switchMap } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { UserLoginCredentials } from '../../types/auth.types';
import { ApiResponse } from '../../types/http.types';
import { UserDetailsResponse } from '../../types/auth.types';

@Injectable({
    providedIn: 'root'
})
export class AuthService {
    constructor(
        private readonly http: HttpClient
    ) {}

    public login(userCredentials: UserLoginCredentials): Observable<ApiResponse<UserDetailsResponse>> {
        return this.http.get<Response>(`${environment.apiUrl}/sanctum/csrf-cookie`, { withCredentials: true }).pipe(
            switchMap(() => {
                return this.http.post<ApiResponse<UserDetailsResponse>>(`${environment.apiUrl}/login`, userCredentials, { withCredentials: true });
            })
        );
    }

    public logout(): Observable<ApiResponse<void>> {
        return this.http.post<ApiResponse<void>>(`${environment.apiUrl}/logout`, {});
    }

    public user(): Observable<ApiResponse<UserDetailsResponse>> {
        return this.http.get<ApiResponse<UserDetailsResponse>>(`${environment.apiUrl}/user`);
    }
}
