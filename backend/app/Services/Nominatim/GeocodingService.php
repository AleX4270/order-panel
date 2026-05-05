<?php
declare(strict_types=1);

namespace App\Services\Nominatim;

use App\Dtos\Api\Address\AddressDto;
use App\Exceptions\Nominatim\Geocoding\CoordinatesNotFoundException;
use Illuminate\Support\Facades\Http;
use App\ValueObjects\Coordinates;
use Exception;

class GeocodingService {
    public function getCoordinates(AddressDto $address): Coordinates {
        $baseUrl = config('app.nominatimApiUrl');
        $queryParams = [
            'street' => $address->address,
            'city' => $address->cityName,
            'country' => $address->countrySymbol,
            'format' => 'json'
        ];

        if(!empty($address->postalCode)) {
            $queryParams['postalcode'] = $address->postalCode;
        }

        $response = Http::get($baseUrl, $queryParams);
        $response->onError(function($res) {
            throw new CoordinatesNotFoundException($res->reason(), $res->status());
        });

        $data = $response->json();

        if(empty($data) || empty($data[0])) {
            throw new CoordinatesNotFoundException('Geocoding result data empty');
        }

        return new Coordinates(
            latitude: (float)$data[0]['lat'],
            longitude: (float)$data[0]['lon'],
        );
    }
}