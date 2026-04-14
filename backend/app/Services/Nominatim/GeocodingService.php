<?php
declare(strict_types=1);

namespace App\Services\Nominatim;

use App\Exceptions\Nominatim\Geocoding\CoordinatesNotFoundException;
use App\Models\Address;
use Illuminate\Support\Facades\Http;
use Coordinates;

class GeocodingService {
    public function getCoordinates(Address $address): Coordinates {
        $baseUrl = config('app.nominatimApiUrl');
        $queryParams = [
            'street' => $address->address,
            'city' => $address->city->name,
            'country' => $address->city->province->country->symbol,
            'format' => 'json'
        ];

        if(!empty($address->postal_code)) {
            $queryParams['postalcode'] = $address->postal_code;
        }

        $response = Http::get($baseUrl, $queryParams);
        $response->onError(function($res) {
            throw new CoordinatesNotFoundException($res->reason(), $res->status());
        });

        $data = $response->json();

        return new Coordinates(
            latitude: (float)$data['lat'],
            longitude: (float)$data['lon'],
        );
    }
}