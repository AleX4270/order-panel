<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Http\Requests\Api\Order\OrderShowRequest;
use App\Models\Address;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Priority;
use App\ValueObjects\Coordinates;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns the requested order', function() {
    createHeadquarters();

    $order = Order::factory()->create([
        'created_at' => '2026-05-15 10:30:00',
        'date_deadline' => '2026-05-29 18:45:00',
    ]);
    $order->translations()->update(['remarks' => 'Call before arrival']);

    $response = actingAsApiUser(PermissionType::ORDERS_SHOW)
        ->getJson('/api/orders/'.$order->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.id', $order->id)
        ->assertJsonPath('data.address', $order->address->address)
        ->assertJsonPath('data.postalCode', $order->address->postal_code)
        ->assertJsonPath('data.cityId', $order->address->city_id)
        ->assertJsonPath('data.cityName', $order->address->city->name)
        ->assertJsonPath('data.provinceId', $order->address->city->province_id)
        ->assertJsonPath('data.provinceName', $order->address->city->province->name)
        ->assertJsonPath('data.countryId', $order->address->city->province->country_id)
        ->assertJsonPath('data.priorityId', $order->priority_id)
        ->assertJsonPath('data.prioritySymbol', $order->priority->symbol)
        ->assertJsonPath('data.priorityName', $order->priority->translations->first()->name)
        ->assertJsonPath('data.statusId', $order->status_id)
        ->assertJsonPath('data.statusSymbol', $order->status->symbol)
        ->assertJsonPath('data.statusName', $order->status->translations->first()->name)
        ->assertJsonPath('data.phoneNumber', $order->client->phone_number)
        ->assertJsonPath('data.remarks', 'Call before arrival')
        ->assertJsonPath('data.dateCreated', '2026-05-15')
        ->assertJsonPath('data.dateDeadline', '2026-05-29')
        ->assertJsonPath('data.dateCompleted', null)
        ->assertJsonPath('data.isOverdue', true);
});

it('returns the completion date and the overdue flag of a completed order', function() {
    createHeadquarters();

    $order = Order::factory()->create([
        'date_deadline' => now()->addDay(),
        'date_completed' => '2026-05-20 08:15:00',
    ]);

    actingAsApiUser(PermissionType::ORDERS_SHOW)
        ->getJson('/api/orders/'.$order->id)
        ->assertOk()
        ->assertJsonPath('data.dateCompleted', '2026-05-20')
        ->assertJsonPath('data.isOverdue', false);
});

it('returns the coordinates and the distance from the headquarters', function() {
    createHeadquarters(52.2297, 21.0122);

    $order = Order::factory()
        ->for(Address::factory()->state([
            'coordinates' => new Coordinates(52.2297, 21.0122),
        ]))
        ->create();

    actingAsApiUser(PermissionType::ORDERS_SHOW)
        ->getJson('/api/orders/'.$order->id)
        ->assertOk()
        ->assertJsonPath('data.coordinates.latitude', 52.2297)
        ->assertJsonPath('data.coordinates.longitude', 21.0122)
        ->assertJsonPath('data.distance', '0.00');
});

it('returns the priority and status names translated to the current locale', function(string $locale, string $expectedPriorityName, string $expectedStatusName, string $expectedRemarks) {
    createHeadquarters();

    $polish = Language::where('symbol', 'pl')->sole();
    $english = Language::where('symbol', 'en')->sole();

    $priority = Priority::factory()->create();
    $priority->translations()->delete();
    $priority->translations()->createMany([
        ['language_id' => $polish->id, 'name' => 'Wysoki'],
        ['language_id' => $english->id, 'name' => 'High'],
    ]);

    $status = OrderStatus::factory()->create();
    $status->translations()->delete();
    $status->translations()->createMany([
        ['language_id' => $polish->id, 'name' => 'W trakcie'],
        ['language_id' => $english->id, 'name' => 'In progress'],
    ]);

    $order = Order::factory()->create([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
    ]);
    $order->translations()->delete();
    $order->translations()->createMany([
        ['language_id' => $polish->id, 'remarks' => 'Uwagi'],
        ['language_id' => $english->id, 'remarks' => 'Remarks'],
    ]);

    App::setLocale($locale);

    actingAsApiUser(PermissionType::ORDERS_SHOW)
        ->getJson('/api/orders/'.$order->id)
        ->assertOk()
        ->assertJsonPath('data.priorityName', $expectedPriorityName)
        ->assertJsonPath('data.statusName', $expectedStatusName)
        ->assertJsonPath('data.remarks', $expectedRemarks);
})->with([
    'polish' => ['locale' => 'pl', 'expectedPriorityName' => 'Wysoki', 'expectedStatusName' => 'W trakcie', 'expectedRemarks' => 'Uwagi'],
    'english' => ['locale' => 'en', 'expectedPriorityName' => 'High', 'expectedStatusName' => 'In progress', 'expectedRemarks' => 'Remarks'],
]);

it('returns the requested order among several', function() {
    createHeadquarters();

    $order = Order::factory()->create();
    Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_SHOW)
        ->getJson('/api/orders/'.$order->id)
        ->assertOk()
        ->assertJsonPath('data.id', $order->id);
});

it('returns a deleted order, unlike the index endpoint', function() {
    createHeadquarters();

    $deletedOrder = Order::factory()->inactive()->create();

    actingAsApiUser(PermissionType::ORDERS_SHOW)
        ->getJson('/api/orders/'.$deletedOrder->id)
        ->assertOk()
        ->assertJsonPath('data.id', $deletedOrder->id);
});

it('returns an error when the order does not exist', function() {
    createHeadquarters();

    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_SHOW)
        ->getJson('/api/orders/'.($order->id + 1000))
        ->assertNotFound();
});

it('returns an error for an id that is not a positive integer', function(string $id) {
    createHeadquarters();

    actingAsApiUser(PermissionType::ORDERS_SHOW)
        ->getJson('/api/orders/'.$id)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('id');
})->with([
    'not a number' => ['id' => 'abc'],
    'zero' => ['id' => '0'],
    'negative' => ['id' => '-3'],
    'decimal' => ['id' => '1.5'],
]);

it('responds with valid api data structure', function() {
    createHeadquarters();

    $order = Order::factory()->create(['date_completed' => now()]);

    $response = actingAsApiUser(PermissionType::ORDERS_SHOW)
        ->getJson('/api/orders/'.$order->id);

    $response
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) =>
            $json->has('timestamp')
                ->has('message')
                ->has('data', fn(AssertableJson $json) =>
                    $json->whereType('id', 'integer')
                        ->whereType('address', 'string')
                        ->whereType('postalCode', 'string|null')
                        ->whereType('cityId', 'integer')
                        ->whereType('cityName', 'string')
                        ->whereType('provinceId', 'integer')
                        ->whereType('provinceName', 'string')
                        ->whereType('countryId', 'integer')
                        ->whereType('priorityId', 'integer')
                        ->whereType('prioritySymbol', 'string')
                        ->whereType('priorityName', 'string')
                        ->whereType('statusId', 'integer')
                        ->whereType('statusSymbol', 'string')
                        ->whereType('statusName', 'string')
                        ->whereType('dateCreated', 'string')
                        ->whereType('dateDeadline', 'string')
                        ->whereType('dateCompleted', 'string')
                        ->whereType('phoneNumber', 'string')
                        ->whereType('remarks', 'string|null')
                        ->whereType('isOverdue', 'boolean')
                        ->whereType('distance', 'string')
                        ->has('coordinates', fn(AssertableJson $json) =>
                            $json->whereType('latitude', 'double')
                                ->whereType('longitude', 'double')
                        )
                )
        );
});

it('returns an error when the company is not configured', function() {
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_SHOW)
        ->getJson('/api/orders/'.$order->id)
        ->assertNotFound();
});

it('has proper validation rules', function() {
    expect(new OrderShowRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    $response = actingAsApiUser()
        ->getJson('api/orders/1');

    $response
        ->assertForbidden();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/orders/1')
        ->assertUnauthorized();
});
