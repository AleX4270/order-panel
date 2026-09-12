<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Http;

function fakeGeocoding(array $result = [['lat' => '52.2297', 'lon' => '21.0122']], int $status = 200): void {
    Http::preventStrayRequests();
    Http::fake([
        config('app.nominatimApiUrl').'*' => Http::response($result, $status),
    ]);
}
