<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Http\Requests\Api\Order\OrderRequest;
use App\Models\Address;
use App\Models\City;
use App\Models\Client;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Priority;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

function createOrderDependencies(): array {
    return [
        City::factory()->create(['name' => 'Warsaw']),
        Priority::factory()->create(),
        OrderStatus::factory()->create(),
    ];
}

function storeOrderPayload(City $city, Priority $priority, OrderStatus $status, array $overrides = []): array {
    return array_merge([
        'countryId' => $city->province->country_id,
        'provinceId' => $city->province_id,
        'cityId' => $city->id,
        'postalCode' => '00-001',
        'address' => 'Main Street 1',
        'phoneNumber' => '+48 111 222 333',
        'priorityId' => $priority->id,
        'statusId' => $status->id,
        'dateCreation' => '2026-05-15',
        'dateDeadline' => '2026-05-29',
        'remarks' => 'Call before arrival',
    ], $overrides);
}

it('creates an order', function() {
    fakeGeocoding();
    [$city, $priority, $status] = createOrderDependencies();
    $actingUser = User::factory()->create();

    $response = actingAsUser($actingUser, PermissionType::ORDERS_CREATE)
        ->postJson('/api/orders', storeOrderPayload($city, $priority, $status));

    $response
        ->assertCreated()
        ->assertJsonPath('message', __('response.created'));

    $order = Order::sole();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'client_id' => Client::sole()->id,
        'address_id' => Address::sole()->id,
        'user_creation_id' => $actingUser->id,
        'user_modification_id' => $actingUser->id,
        'created_at' => '2026-05-15 00:00:00',
        'date_deadline' => '2026-05-29 00:00:00',
        'date_completed' => null,
        'is_active' => true,
    ])
        ->assertDatabaseHas('order_translations', [
            'order_id' => $order->id,
            'language_id' => Language::where('symbol', app()->getLocale())->sole()->id,
            'remarks' => 'Call before arrival',
        ]);
});

it('generates a symbol for the order', function() {
    fakeGeocoding();
    [$city, $priority, $status] = createOrderDependencies();

    actingAsApiUser(PermissionType::ORDERS_CREATE)
        ->postJson('/api/orders', storeOrderPayload($city, $priority, $status))
        ->assertCreated();

    expect(Order::sole()->symbol)->toBeString()->toHaveLength(16);
});

it('creates an order without remarks and a postal code', function() {
    fakeGeocoding();
    [$city, $priority, $status] = createOrderDependencies();

    $payload = storeOrderPayload($city, $priority, $status);
    unset($payload['remarks'], $payload['postalCode']);

    actingAsApiUser(PermissionType::ORDERS_CREATE)
        ->postJson('/api/orders', $payload)
        ->assertCreated();

    Http::assertSent(function(Request $request) {
        parse_str(parse_url($request->url(), PHP_URL_QUERY), $query);

        return !array_key_exists('postalcode', $query);
    });

    $this->assertDatabaseHas('addresses', [
        'address' => 'Main Street 1',
        'postal_code' => null,
    ])
        ->assertDatabaseHas('order_translations', [
            'order_id' => Order::sole()->id,
            'remarks' => null,
        ]);
});

it('geocodes a new address and stores its coordinates', function() {
    fakeGeocoding([['lat' => '52.2297', 'lon' => '21.0122']]);
    [$city, $priority, $status] = createOrderDependencies();

    actingAsApiUser(PermissionType::ORDERS_CREATE)
        ->postJson('/api/orders', storeOrderPayload($city, $priority, $status))
        ->assertCreated();

    Http::assertSent(function(Request $request) use($city) {
        parse_str(parse_url($request->url(), PHP_URL_QUERY), $query);

        return $query === [
            'street' => 'Main Street 1',
            'city' => 'Warsaw',
            'country' => $city->province->country->symbol,
            'format' => 'json',
            'postalcode' => '00-001',
        ];
    });

    $address = Address::sole();

    expect($address->latitude)->toBe(52.2297)
        ->and($address->longitude)->toBe(21.0122);
});

it('reuses an existing address without geocoding it again', function() {
    fakeGeocoding();
    [$city, $priority, $status] = createOrderDependencies();
    $address = Address::factory()->for($city)->create([
        'address' => 'Main Street 1',
        'postal_code' => '00-001',
    ]);

    actingAsApiUser(PermissionType::ORDERS_CREATE)
        ->postJson('/api/orders', storeOrderPayload($city, $priority, $status))
        ->assertCreated();

    Http::assertNothingSent();

    $this->assertDatabaseCount('addresses', 1)
        ->assertDatabaseHas('orders', ['address_id' => $address->id]);
});

it('creates a new city when the city id is 0 and a name is given', function() {
    fakeGeocoding();
    [$city, $priority, $status] = createOrderDependencies();

    actingAsApiUser(PermissionType::ORDERS_CREATE)
        ->postJson('/api/orders', storeOrderPayload($city, $priority, $status, [
            'cityId' => 0,
            'cityName' => 'Newtown',
        ]))
        ->assertCreated();

    $this->assertDatabaseCount('cities', 2)
        ->assertDatabaseHas('cities', [
            'name' => 'Newtown',
            'province_id' => $city->province_id,
        ])
        ->assertDatabaseHas('addresses', [
            'city_id' => City::where('name', 'Newtown')->sole()->id,
        ]);
});

it('assigns the client with the given phone number', function() {
    fakeGeocoding();
    [$city, $priority, $status] = createOrderDependencies();
    $client = Client::factory()->create(['phone_number' => '+48 111 222 333']);
    Client::factory()->create(['phone_number' => '+48 999 999 999']);

    actingAsApiUser(PermissionType::ORDERS_CREATE)
        ->postJson('/api/orders', storeOrderPayload($city, $priority, $status))
        ->assertCreated();

    $this->assertDatabaseCount('clients', 2)
        ->assertDatabaseHas('orders', ['client_id' => $client->id]);
});

it('creates a client when the phone number is unknown', function() {
    fakeGeocoding();
    [$city, $priority, $status] = createOrderDependencies();

    actingAsApiUser(PermissionType::ORDERS_CREATE)
        ->postJson('/api/orders', storeOrderPayload($city, $priority, $status))
        ->assertCreated();

    $this->assertDatabaseCount('clients', 1)
        ->assertDatabaseHas('clients', [
            'phone_number' => '+48 111 222 333',
            'first_name' => null,
            'last_name' => null,
            'email' => null,
        ])
        ->assertDatabaseHas('orders', ['client_id' => Client::sole()->id]);
});

it('returns an error when the city does not exist', function() {
    fakeGeocoding();
    [$city, $priority, $status] = createOrderDependencies();

    actingAsApiUser(PermissionType::ORDERS_CREATE)
        ->postJson('/api/orders', storeOrderPayload($city, $priority, $status, [
            'cityId' => $city->id + 1000,
        ]))
        ->assertNotFound();

    $this->assertDatabaseCount('orders', 0);
});

it('does not store anything when the validation fails', function() {
    fakeGeocoding();
    [$city, $priority, $status] = createOrderDependencies();

    actingAsApiUser(PermissionType::ORDERS_CREATE)
        ->postJson('/api/orders', storeOrderPayload($city, $priority, $status, [
            'dateDeadline' => '2026-05-14',
        ]))
        ->assertUnprocessable();

    Http::assertNothingSent();

    $this->assertDatabaseCount('orders', 0)
        ->assertDatabaseCount('clients', 0)
        ->assertDatabaseCount('addresses', 0);
});

it('has proper validation rules', function() {
    expect(new OrderRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    [$city, $priority, $status] = createOrderDependencies();

    actingAsApiUser()
        ->postJson('/api/orders', storeOrderPayload($city, $priority, $status))
        ->assertForbidden();

    $this->assertDatabaseCount('orders', 0);
});

it('returns an error on unauthorized request', function() {
    [$city, $priority, $status] = createOrderDependencies();

    $this->postJson('/api/orders', storeOrderPayload($city, $priority, $status))
        ->assertUnauthorized();

    $this->assertDatabaseCount('orders', 0);
});
