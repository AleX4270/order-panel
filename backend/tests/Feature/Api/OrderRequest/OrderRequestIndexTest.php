<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Http\Requests\Api\OrderRequest\OrderRequestFilterRequest;
use App\Models\Address;
use App\Models\City;
use App\Models\Client;
use App\Models\OrderRequest;
use App\ValueObjects\Coordinates;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Testing\Fluent\AssertableJson;

function createOrderRequestsWithDates(): Collection {
    return OrderRequest::factory()
        ->count(5)
        ->state(new Sequence(
            ['id' => 1, 'created_at' => '2026-05-14 23:59:59'],
            ['id' => 2, 'created_at' => '2026-05-15 00:00:00'],
            ['id' => 3, 'created_at' => '2026-05-15 12:30:45'],
            ['id' => 4, 'created_at' => '2026-05-15 23:59:59'],
            ['id' => 5, 'created_at' => '2026-05-16 00:00:00'],
        ))
        ->create();
}

function createOrderRequestsForSorting(): Collection {
    return OrderRequest::factory()
        ->count(4)
        ->state(new Sequence(
            ['id' => 1, 'created_at' => '2026-03-02 10:00:00'],
            ['id' => 2, 'created_at' => '2026-03-04 10:00:00'],
            ['id' => 3, 'created_at' => '2026-03-01 10:00:00'],
            ['id' => 4, 'created_at' => '2026-03-03 10:00:00'],
        ))
        ->create();
}

function createOrderRequestsAtDistances(): Collection {
    return OrderRequest::factory()
        ->count(4)
        ->state(new Sequence(
            ['id' => 1, 'address_id' => Address::factory()->state(['coordinates' => new Coordinates(52.0, 21.0)])],
            ['id' => 2, 'address_id' => Address::factory()->state(['coordinates' => new Coordinates(52.5, 21.0)])],
            ['id' => 3, 'address_id' => Address::factory()->state(['coordinates' => new Coordinates(53.0, 21.0)])],
            ['id' => 4, 'address_id' => Address::factory()->state(['coordinates' => new Coordinates(54.0, 21.0)])],
        ))
        ->create();
}

it('returns all order requests', function() {
    createHeadquarters();

    $orderRequests = OrderRequest::factory()
        ->count(5)
        ->create()
        ->sortByDesc('id')
        ->values();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data.items')
        ->assertJsonPath('data.count', 5)
        ->assertJsonPath('data.items.*.id', $orderRequests->pluck('id')->all())
        ->assertJsonPath('data.items.*.remarks', $orderRequests->pluck('remarks')->all())
        ->assertJsonPath('data.items.*.firstName', $orderRequests->pluck('client.first_name')->all())
        ->assertJsonPath('data.items.*.lastName', $orderRequests->pluck('client.last_name')->all())
        ->assertJsonPath('data.items.*.email', $orderRequests->pluck('client.email')->all())
        ->assertJsonPath('data.items.*.phoneNumber', $orderRequests->pluck('client.phone_number')->all())
        ->assertJsonPath('data.items.*.address', $orderRequests->pluck('address.address')->all())
        ->assertJsonPath('data.items.*.postalCode', $orderRequests->pluck('address.postal_code')->all())
        ->assertJsonPath('data.items.*.cityName', $orderRequests->pluck('address.city.name')->all())
        ->assertJsonPath('data.items.*.provinceName', $orderRequests->pluck('address.city.province.name')->all());
});

it('returns the coordinates of the order request address', function() {
    createHeadquarters();

    OrderRequest::factory()
        ->for(Address::factory()->state([
            'coordinates' => new Coordinates(50.061389, 19.938333),
        ]))
        ->create();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests');

    $response
        ->assertOk()
        ->assertJsonPath('data.items.0.coordinates.latitude', 50.061389)
        ->assertJsonPath('data.items.0.coordinates.longitude', 19.938333);
});

it('returns the distance from the headquarters in kilometers', function() {
    createHeadquarters();
    createOrderRequestsAtDistances();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests?'.http_build_query(['sortColumn' => 'orderRequestNumber', 'sortDir' => 'asc']));

    $response
        ->assertOk()
        ->assertJsonPath('data.items.0.distance', '0.00');

    $distances = array_map('floatval', $response->json('data.items.*.distance'));

    expect($distances[1])->toBeGreaterThan(55.0)->toBeLessThan(56.0)
        ->and($distances[2])->toBeGreaterThan(111.0)->toBeLessThan(112.0)
        ->and($distances[3])->toBeGreaterThan(222.0)->toBeLessThan(223.0);
});

it('returns order requests from newest to oldest by default', function() {
    createHeadquarters();

    $orderRequests = OrderRequest::factory()
        ->count(3)
        ->create();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $orderRequests->sortByDesc('id')->pluck('id')->values()->all());
});

it('does not return deleted order requests', function() {
    createHeadquarters();

    $orderRequests = OrderRequest::factory()
        ->count(3)
        ->create();

    $deletedOrderRequest = OrderRequest::factory()->create();
    $deletedOrderRequest->delete();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(3, 'data.items')
        ->assertJsonPath('data.count', 3)
        ->assertJsonPath('data.items.*.id', $orderRequests->sortByDesc('id')->pluck('id')->values()->all())
        ->assertJsonMissing(['id' => $deletedOrderRequest->id]);
});

it('returns order requests matching provided term (allFields)', function(string $term, array $expectedIds) {
    createHeadquarters();

    OrderRequest::factory()
        ->count(4)
        ->state(new Sequence(
            [
                'id' => 1,
                'remarks' => 'Urgent delivery',
                'client_id' => Client::factory()->state([
                    'first_name' => 'John',
                    'last_name' => 'Reacher',
                    'email' => 'john@system.local',
                    'phone_number' => '+48 111 222 333',
                ]),
                'address_id' => Address::factory()->state([
                    'address' => 'Main Street 1',
                    'postal_code' => '00-001',
                    'city_id' => City::factory()->state(['name' => 'Warsaw']),
                ]),
            ],
            [
                'id' => 2,
                'remarks' => 'Poznan office',
                'client_id' => Client::factory()->state([
                    'first_name' => 'Anne',
                    'last_name' => 'Reacher',
                    'email' => 'anne@system.local',
                    'phone_number' => '+48 444 555 666',
                ]),
                'address_id' => Address::factory()->state([
                    'address' => 'Long Road 22',
                    'postal_code' => '30-002',
                    'city_id' => City::factory()->state(['name' => 'Krakow']),
                ]),
            ],
            [
                'id' => 13,
                'remarks' => 'Call before arrival',
                'client_id' => Client::factory()->state([
                    'first_name' => 'Maria',
                    'last_name' => 'Smith',
                    'email' => 'm.smith@external.com',
                    'phone_number' => '+48 777 888 999',
                ]),
                'address_id' => Address::factory()->state([
                    'address' => 'Short Lane 13',
                    'postal_code' => '60-013',
                    'city_id' => City::factory()->state(['name' => 'Poznan']),
                ]),
            ],
            [
                'id' => 45,
                'remarks' => null,
                'client_id' => Client::factory()->state([
                    'first_name' => null,
                    'last_name' => null,
                    'email' => 'ghost@nowhere.test',
                    'phone_number' => '+48 222 333 444',
                ]),
                'address_id' => Address::factory()->state([
                    'address' => 'Ghost Alley 4',
                    'postal_code' => '99-999',
                    'city_id' => City::factory()->state(['name' => 'Gdansk']),
                ]),
            ],
        ))
        ->create();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests?'.http_build_query(['allFields' => $term]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'address, exact' => ['term' => 'Main Street 1', 'expectedIds' => [1]],
    'address, infix in a different case' => ['term' => 'long road', 'expectedIds' => [2]],
    'postal code, exact' => ['term' => '30-002', 'expectedIds' => [2]],
    'postal code, prefix' => ['term' => '60-', 'expectedIds' => [13]],
    'city name, exact' => ['term' => 'Krakow', 'expectedIds' => [2]],
    'city name, different case' => ['term' => 'gdansk', 'expectedIds' => [45]],
    'email, exact' => ['term' => 'john@system.local', 'expectedIds' => [1]],
    'email, shared domain' => ['term' => '@system.local', 'expectedIds' => [2, 1]],
    'email, different case' => ['term' => 'M.SMITH', 'expectedIds' => [13]],
    'phone number, infix' => ['term' => '444 555', 'expectedIds' => [2]],
    'remarks, infix' => ['term' => 'arrival', 'expectedIds' => [13]],
    'id, exact' => ['term' => '13', 'expectedIds' => [13]],
    'id, partial matches every field containing the digit' => ['term' => '1', 'expectedIds' => [13, 1]],
    'full name, first name only' => ['term' => 'Maria', 'expectedIds' => [13]],
    'full name, last name shared by two clients' => ['term' => 'Reacher', 'expectedIds' => [2, 1]],
    'full name, last name in a different case' => ['term' => 'reacher', 'expectedIds' => [2, 1]],
    'full name, first and last name joined' => ['term' => 'Maria Smith', 'expectedIds' => [13]],
    'full name, term spanning the separating space' => ['term' => 'n Rea', 'expectedIds' => [1]],
    'full name, parts taken from two different clients' => ['term' => 'John Smith', 'expectedIds' => []],
    'term matching a city of one request and remarks of another' => ['term' => 'Poznan', 'expectedIds' => [13, 2]],
    'term matching nothing' => ['term' => 'nonexistent', 'expectedIds' => []],
    'term matching nothing, client without a first and last name' => ['term' => 'Ghost Ghost', 'expectedIds' => []],
    'empty term returns every order request' => ['term' => '', 'expectedIds' => [45, 13, 2, 1]],
    'term "0" is treated as empty and returns every order request' => ['term' => '0', 'expectedIds' => [45, 13, 2, 1]],
]);

it('returns order requests matching provided creation date, scoped to the whole day', function(string $date, array $expectedIds) {
    createHeadquarters();
    createOrderRequestsWithDates();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests?'.http_build_query(['dateCreation' => $date]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'whole day including both boundaries' => ['date' => '2026-05-15', 'expectedIds' => [4, 3, 2]],
    'last second of the previous day' => ['date' => '2026-05-14', 'expectedIds' => [1]],
    'first second of the next day' => ['date' => '2026-05-16', 'expectedIds' => [5]],
    'day without any order request' => ['date' => '2026-05-17', 'expectedIds' => []],
    'time part of the term is ignored' => ['date' => '2026-05-15 18:45:00', 'expectedIds' => [4, 3, 2]],
]);

it('returns order requests within the given distance from the headquarters', function(string $distance, array $expectedIds) {
    createHeadquarters();
    createOrderRequestsAtDistances();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests?'.http_build_query(['distanceFromHeadquarters' => $distance]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'only the request at the headquarters' => ['distance' => '1', 'expectedIds' => [1]],
    'requests up to half a degree away' => ['distance' => '60', 'expectedIds' => [2, 1]],
    'requests up to a degree away' => ['distance' => '120', 'expectedIds' => [3, 2, 1]],
    'every request' => ['distance' => '300', 'expectedIds' => [4, 3, 2, 1]],
    'distance "0" is treated as no filter and returns every request' => ['distance' => '0', 'expectedIds' => [4, 3, 2, 1]],
]);

it('narrows results when several filters are provided', function() {
    createHeadquarters();

    OrderRequest::factory()
        ->count(3)
        ->state(new Sequence(
            [
                'id' => 1,
                'created_at' => '2026-05-15 10:00:00',
                'address_id' => Address::factory()->state(['coordinates' => new Coordinates(52.0, 21.0)]),
            ],
            [
                'id' => 2,
                'created_at' => '2026-05-15 12:00:00',
                'address_id' => Address::factory()->state(['coordinates' => new Coordinates(54.0, 21.0)]),
            ],
            [
                'id' => 3,
                'created_at' => '2026-05-16 10:00:00',
                'address_id' => Address::factory()->state(['coordinates' => new Coordinates(52.0, 21.0)]),
            ],
        ))
        ->create();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests?'.http_build_query([
            'dateCreation' => '2026-05-15',
            'distanceFromHeadquarters' => '10',
        ]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.count', 1)
        ->assertJsonPath('data.items.*.id', [1]);
});

it('returns paginated data', function(int $page, int $size, int $itemsCount, int $expectedSize) {
    createHeadquarters();

    $orderRequests = OrderRequest::factory()
        ->count($itemsCount)
        ->create();

    $expectedIds = $orderRequests
        ->sortByDesc('id')
        ->slice(($page - 1) * $size, $size)
        ->pluck('id')
        ->all();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests?'.http_build_query(['page' => $page, 'pageSize' => $size]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount($expectedSize, 'data.items')
        ->assertJsonPath('data.count', $itemsCount)
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with('pagination');

it('returns data sorted by the requested column and direction', function(string $sortColumn, string $sortDir, array $expectedIds) {
    createHeadquarters();
    createOrderRequestsForSorting();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests?'.http_build_query([
            'sortColumn' => $sortColumn,
            'sortDir' => $sortDir,
        ]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'orderRequestNumber, ascending' => ['sortColumn' => 'orderRequestNumber', 'sortDir' => 'asc', 'expectedIds' => [1, 2, 3, 4]],
    'orderRequestNumber, descending' => ['sortColumn' => 'orderRequestNumber', 'sortDir' => 'desc', 'expectedIds' => [4, 3, 2, 1]],
    'dateCreated, ascending' => ['sortColumn' => 'dateCreated', 'sortDir' => 'asc', 'expectedIds' => [3, 1, 4, 2]],
    'dateCreated, descending' => ['sortColumn' => 'dateCreated', 'sortDir' => 'desc', 'expectedIds' => [2, 4, 1, 3]],
]);

it('falls back to descending id when the sort column is not supported', function(array $params) {
    createHeadquarters();
    createOrderRequestsForSorting();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests?'.http_build_query($params));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', [4, 3, 2, 1]);
})->with([
    'no sorting parameters at all' => ['params' => []],
    'unknown sort column' => ['params' => ['sortColumn' => 'email', 'sortDir' => 'asc']],
    'direction without a column is ignored' => ['params' => ['sortDir' => 'asc']],
]);

it('responds with valid api data structure', function() {
    createHeadquarters();

    OrderRequest::factory()
        ->count(3)
        ->create();

    OrderRequest::factory()
        ->create(['remarks' => null]);

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests');

    $response
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) =>
            $json->has('timestamp')
                ->has('message')
                ->has('data', fn(AssertableJson $json) =>
                    $json->has('count')
                        ->has('items', 4, fn(AssertableJson $json) =>
                            $json->whereType('id', 'integer')
                                ->whereType('firstName', 'string')
                                ->whereType('lastName', 'string')
                                ->whereType('email', 'string')
                                ->whereType('phoneNumber', 'string')
                                ->whereType('address', 'string')
                                ->whereType('postalCode', 'string')
                                ->whereType('cityName', 'string')
                                ->whereType('provinceName', 'string')
                                ->whereType('remarks', 'string|null')
                                ->whereType('dateCreated', 'string')
                                ->whereType('distance', 'string')
                                ->has('coordinates', fn(AssertableJson $json) =>
                                    $json->whereType('latitude', 'double')
                                        ->whereType('longitude', 'double')
                                )
                        )
                )
        );
});

it('returns an error when the company is not configured', function() {
    OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_VIEW)
        ->getJson('/api/order-requests')
        ->assertNotFound();
});

it('has proper validation rules', function() {
    expect(new OrderRequestFilterRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    $response = actingAsApiUser()
        ->getJson('api/order-requests');

    $response
        ->assertForbidden();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/order-requests')
        ->assertUnauthorized();
});
