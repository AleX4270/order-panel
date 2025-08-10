import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { NavbarElement } from '../../types/navbar.types';

@Injectable({
    providedIn: 'root'
})
export class LocalDataService {
    constructor(
        private readonly http: HttpClient
    ) {}

    public getNavbarData(): Observable<NavbarElement[]> {
        return this.http.get<NavbarElement[]>(`./assets/navbar.json`);
    }
}
