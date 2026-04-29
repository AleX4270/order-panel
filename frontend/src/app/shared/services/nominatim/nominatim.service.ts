import { Injectable } from '@angular/core';
import { RestService } from '../api/rest/rest.service';
import { ApiResponse } from '../../types/rest.types';
import { NominatimLocationFilterParams, NominatimLocationItem } from '../../types/nominatim.types';
import { environment } from '../../../../environments/environment';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class NominatimService extends RestService {
    public getCoordinates(params: NominatimLocationFilterParams): Observable<NominatimLocationItem[]> {
        return this.http.get<NominatimLocationItem[]>(environment.map.geocodingApiUrl + this.getQueryParams({...params, format: 'json'}));
    }
}
