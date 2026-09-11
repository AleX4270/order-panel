<?php
declare(strict_types=1);

use App\Enums\NotificationChannelType;
use App\Enums\NotificationEventType;
use App\Events\OrderRequestCreated;
use App\Http\Requests\Api\OrderRequest\OrderRequestRequest;
use App\Models\Address;
use App\Models\City;
use App\Models\Client;
use App\Models\NotificationChannel;
use App\Models\NotificationEvent;
use App\Models\OrderRequest;
use App\Models\Province;
use App\Models\User;
use App\Notifications\OrderRequestCreatedNotification;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

function storeOrderRequestPayload(Province $province, array $overrides = []): array {
    return array_merge([
        'firstName' => 'Maria',
        'lastName' => 'Smith',
        'email' => 'm.smith@system.local',
        'phoneNumber' => '+48 111 222 333',
        'countryId' => $province->country_id,
        'provinceId' => $province->id,
        'city' => 'Warsaw',
        'postalCode' => '00-001',
        'address' => 'Main Street 1',
        'remarks' => 'Call before arrival',
        'isConsentGiven' => true,
    ], $overrides);
}

function fakeGeocoding(array $result = [['lat' => '52.2297', 'lon' => '21.0122']], int $status = 200): void {
    Http::preventStrayRequests();
    Http::fake([
        config('app.nominatimApiUrl').'*' => Http::response($result, $status),
    ]);
}

it('creates an order request', function() {
    fakeGeocoding();
    $province = Province::factory()->create();

    $response = $this->postJson('/api/order-requests', storeOrderRequestPayload($province));

    $response
        ->assertCreated()
        ->assertJsonPath('message', __('response.created'));

    $client = Client::sole();
    $address = Address::sole();

    $this->assertDatabaseHas('clients', [
        'first_name' => 'Maria',
        'last_name' => 'Smith',
        'email' => 'm.smith@system.local',
        'phone_number' => '+48 111 222 333',
    ])
        ->assertDatabaseHas('cities', [
            'name' => 'Warsaw',
            'province_id' => $province->id,
        ])
        ->assertDatabaseHas('addresses', [
            'address' => 'Main Street 1',
            'postal_code' => '00-001',
            'city_id' => City::sole()->id,
        ])
        ->assertDatabaseHas('order_requests', [
            'client_id' => $client->id,
            'address_id' => $address->id,
            'remarks' => 'Call before arrival',
        ]);

    expect(OrderRequest::sole()->consent_given_at)->not->toBeNull();
});

it('stores the ip address and the user agent of the requester', function() {
    fakeGeocoding();
    $province = Province::factory()->create();

    $this->withHeaders(['User-Agent' => 'PestAgent/1.0'])
        ->postJson('/api/order-requests', storeOrderRequestPayload($province))
        ->assertCreated();

    $this->assertDatabaseHas('order_requests', [
        'ip_address' => '127.0.0.1',
        'user_agent' => 'PestAgent/1.0',
    ]);
});

it('stores the email in lower case', function() {
    fakeGeocoding();
    $province = Province::factory()->create();

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province, [
        'email' => 'M.Smith@System.LOCAL',
    ]))
        ->assertCreated();

    $this->assertDatabaseHas('clients', ['email' => 'm.smith@system.local']);
});

it('stores the alias of the client', function() {
    fakeGeocoding();
    $province = Province::factory()->create();

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province, [
        'alias' => 'Mimi',
    ]))
        ->assertCreated();

    $this->assertDatabaseHas('clients', ['alias' => 'Mimi']);
});

it('creates an order request without remarks', function() {
    fakeGeocoding();
    $province = Province::factory()->create();

    $payload = storeOrderRequestPayload($province);
    unset($payload['remarks']);

    $this->postJson('/api/order-requests', $payload)
        ->assertCreated();

    $this->assertDatabaseHas('order_requests', ['remarks' => null]);
});

it('geocodes the address and stores its coordinates', function() {
    fakeGeocoding([['lat' => '52.2297', 'lon' => '21.0122']]);
    $province = Province::factory()->create();

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province))
        ->assertCreated();

    Http::assertSent(function(Request $request) use($province) {
        parse_str(parse_url($request->url(), PHP_URL_QUERY), $query);

        return $query === [
            'street' => 'Main Street 1',
            'city' => 'Warsaw',
            'country' => $province->country->symbol,
            'format' => 'json',
            'postalcode' => '00-001',
        ];
    });

    $address = Address::sole();

    expect($address->latitude)->toBe(52.2297)
        ->and($address->longitude)->toBe(21.0122);
});

it('reuses an existing city of the province regardless of the letter case', function() {
    fakeGeocoding();
    $province = Province::factory()->create();
    $city = City::factory()->for($province)->create(['name' => 'Warsaw']);

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province, [
        'city' => 'wArSaW',
    ]))
        ->assertCreated();

    $this->assertDatabaseCount('cities', 1)
        ->assertDatabaseHas('addresses', ['city_id' => $city->id]);
});

it('creates a new city when the name exists only in another province', function() {
    fakeGeocoding();
    $province = Province::factory()->create();
    $otherProvince = Province::factory()->create();
    City::factory()->for($otherProvince)->create(['name' => 'Warsaw']);

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province))
        ->assertCreated();

    $this->assertDatabaseCount('cities', 2)
        ->assertDatabaseHas('cities', [
            'name' => 'Warsaw',
            'province_id' => $province->id,
        ]);
});

it('reuses an existing address without geocoding it again', function() {
    fakeGeocoding();
    $province = Province::factory()->create();
    $city = City::factory()->for($province)->create(['name' => 'Warsaw']);
    $address = Address::factory()->for($city)->create([
        'address' => 'Main Street 1',
        'postal_code' => '00-001',
    ]);

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province))
        ->assertCreated();

    Http::assertNothingSent();

    $this->assertDatabaseCount('addresses', 1)
        ->assertDatabaseHas('order_requests', ['address_id' => $address->id]);
});

it('creates a new client for every order request', function() {
    fakeGeocoding();
    $province = Province::factory()->create();
    Client::factory()->create([
        'email' => 'm.smith@system.local',
        'phone_number' => '+48 111 222 333',
    ]);

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province))
        ->assertCreated();

    $this->assertDatabaseCount('clients', 2);
});

it('dispatches the order request created event', function() {
    Event::fake([OrderRequestCreated::class]);
    fakeGeocoding();
    $province = Province::factory()->create();

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province))
        ->assertCreated();

    Event::assertDispatched(OrderRequestCreated::class, fn(OrderRequestCreated $event) =>
        $event->orderRequest->is(OrderRequest::sole())
    );
});

it('notifies only the users subscribed to the order request created event', function() {
    Notification::fake();
    fakeGeocoding();
    $province = Province::factory()->create();

    $event = NotificationEvent::factory()->create(['symbol' => NotificationEventType::ORDER_REQUEST_CREATED->value]);
    $otherEvent = NotificationEvent::factory()->create(['symbol' => NotificationEventType::ORDER_COMPLETED->value]);
    $channel = NotificationChannel::factory()->create(['symbol' => NotificationChannelType::MAIL->value]);

    $subscribedUser = User::factory()->create();
    $subscribedUser->notificationSettings()->create([
        'notification_event_id' => $event->id,
        'notification_channel_id' => $channel->id,
    ]);

    $userSubscribedToOtherEvent = User::factory()->create();
    $userSubscribedToOtherEvent->notificationSettings()->create([
        'notification_event_id' => $otherEvent->id,
        'notification_channel_id' => $channel->id,
    ]);

    $unsubscribedUser = User::factory()->create();

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province))
        ->assertCreated();

    Notification::assertSentTo($subscribedUser, OrderRequestCreatedNotification::class);
    Notification::assertNotSentTo($userSubscribedToOtherEvent, OrderRequestCreatedNotification::class);
    Notification::assertNotSentTo($unsubscribedUser, OrderRequestCreatedNotification::class);
});

it('returns an error and stores nothing when the address cannot be geocoded', function() {
    fakeGeocoding([]);
    $province = Province::factory()->create();

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province))
        ->assertInternalServerError()
        ->assertJsonPath('message', __('response.addressNotFound'));

    $this->assertDatabaseCount('order_requests', 0)
        ->assertDatabaseCount('clients', 0)
        ->assertDatabaseCount('addresses', 0)
        ->assertDatabaseCount('cities', 0);
});

it('returns an error and stores nothing when the geocoding service fails', function() {
    fakeGeocoding([], 503);
    $province = Province::factory()->create();

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province))
        ->assertInternalServerError()
        ->assertJsonPath('message', __('response.addressNotFound'));

    $this->assertDatabaseCount('order_requests', 0)
        ->assertDatabaseCount('clients', 0)
        ->assertDatabaseCount('addresses', 0)
        ->assertDatabaseCount('cities', 0);
});

it('does not store anything when the validation fails', function() {
    fakeGeocoding();
    $province = Province::factory()->create();

    $this->postJson('/api/order-requests', storeOrderRequestPayload($province, [
        'email' => 'not-an-email',
    ]))
        ->assertUnprocessable();

    Http::assertNothingSent();

    $this->assertDatabaseCount('order_requests', 0)
        ->assertDatabaseCount('clients', 0)
        ->assertDatabaseCount('addresses', 0)
        ->assertDatabaseCount('cities', 0);
});

it('has proper validation rules', function() {
    expect(new OrderRequestRequest()->rules())->toMatchSnapshot();
});
