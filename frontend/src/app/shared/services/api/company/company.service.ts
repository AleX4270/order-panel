import { Injectable } from '@angular/core';
import { RestService } from '../rest/rest.service';
import { Observable } from 'rxjs';
import { ApiResponse } from '../../../types/rest.types';
import { CompanyItem, CompanyParams } from '../../../types/company.types';

@Injectable({
  providedIn: 'root',
})
export class CompanyService extends RestService {
    public show(): Observable<ApiResponse<CompanyItem>> {
        return this.http.get<ApiResponse<CompanyItem>>(`${this.apiUrl}/company`);
    }

    public update(params: CompanyParams): Observable<ApiResponse<void>> {
        return this.http.put<ApiResponse<void>>(`${this.apiUrl}/company`, params);
    }
}
