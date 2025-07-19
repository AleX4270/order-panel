import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { switchMap } from 'rxjs';
import { environment } from '../../../../environments/environment';
import { UserLoginCredentials } from '../../types/auth.types';

@Injectable({
    providedIn: 'root'
})
export class AuthService {
    constructor(
        private readonly http: HttpClient
    ) {}

    public login(userCredentials: UserLoginCredentials): Observable<Response> {
        return this.http.get<Response>(`${environment.apiUrl}/sanctum/csrf-cookie`, { withCredentials: true }).pipe(
            switchMap(() => {
                return this.http.post<Response>(`${environment.apiUrl}/login`, userCredentials, { withCredentials: true });
            })
        );
    }

    public logout(): Observable<Response> {
        return this.http.post<Response>(`${environment.apiUrl}/logout`, {});
    }

    public user(): Observable<Response> {
        return this.http.get<Response>(`${environment.apiUrl}/user`);
    }
}
