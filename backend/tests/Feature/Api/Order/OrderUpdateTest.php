<?php
declare(strict_types=1);

use App\Enums\NotificationChannelType;
use App\Enums\NotificationEventType;
use App\Enums\PermissionType;
use App\Events\OrderCompleted;
use App\Http\Requests\Api\Order\OrderRequest;
use App\Models\Address;
use App\Models\Client;
use App\Models\NotificationChannel;
use App\Models\NotificationEvent;
use App\Models\Order;
use App\Models\Priority;
use App\Models\User;
use App\Notifications\OrderCompletedNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

function updateOrderPayload(Order $order, array $overrides = []): array {
    $city = $order->address->city;

    return array_merge([
        'id' => $order->id,
        'countryId' => $city->province->country_id,
        'provinceId' => $city->province_id,
        'cityId' => $city->id,
        'postalCode' => $order->address->postal_code,
        'address' => $order->address->address,
        'phoneNumber' => $order->client->phone_number,
        'priorityId' => $order->priority_id,
        'statusId' => $order->status_id,
        'dateCreation' => '2026-05-15',
        'dateDeadline' => '2026-05-29',
        'remarks' => 'Updated remarks',
    ], $overrides);
}

it('updates the requested order', function() {
    fakeGeocoding();
    createCompletedOrderStatus();
    $order = Order::factory()->create();
    $newPriority = Priority::factory()->create();
    $actingUser = User::factory()->create();

    actingAsUser($actingUser, PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'priorityId' => $newPriority->id,
        ]))
        ->assertNoContent();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'symbol' => $order->symbol,
        'priority_id' => $newPriority->id,
        'user_creation_id' => $order->user_creation_id,
        'user_modification_id' => $actingUser->id,
        'created_at' => '2026-05-15 00:00:00',
        'date_deadline' => '2026-05-29 00:00:00',
    ])
        ->assertDatabaseHas('order_translations', [
            'order_id' => $order->id,
            'remarks' => 'Updated remarks',
        ]);
});

it('does not touch the other orders', function() {
    fakeGeocoding();
    createCompletedOrderStatus();
    $order = Order::factory()->create();
    $untouchedOrder = Order::factory()->create(['date_deadline' => '2026-12-24 10:00:00']);

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order))
        ->assertNoContent();

    $this->assertDatabaseHas('orders', [
        'id' => $untouchedOrder->id,
        'date_deadline' => '2026-12-24 10:00:00',
        'user_modification_id' => $untouchedOrder->user_modification_id,
    ]);
});

it('moves the order to a new address and geocodes it', function() {
    fakeGeocoding([['lat' => '52.2297', 'lon' => '21.0122']]);
    createCompletedOrderStatus();
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'address' => 'New Street 5',
            'postalCode' => '11-111',
        ]))
        ->assertNoContent();

    $newAddress = Address::where('address', 'New Street 5')->sole();

    expect($newAddress->latitude)->toBe(52.2297)
        ->and($newAddress->longitude)->toBe(21.0122);

    $this->assertDatabaseCount('addresses', 2)
        ->assertDatabaseHas('orders', [
            'id' => $order->id,
            'address_id' => $newAddress->id,
        ]);
});

it('keeps the address when it has not changed', function() {
    fakeGeocoding();
    createCompletedOrderStatus();
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order))
        ->assertNoContent();

    Http::assertNothingSent();

    $this->assertDatabaseCount('addresses', 1)
        ->assertDatabaseHas('orders', [
            'id' => $order->id,
            'address_id' => $order->address_id,
        ]);
});

it('assigns the client with the given phone number', function() {
    fakeGeocoding();
    createCompletedOrderStatus();
    $order = Order::factory()->create();
    $newClient = Client::factory()->create(['phone_number' => '+48 999 888 777']);

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'phoneNumber' => '+48 999 888 777',
        ]))
        ->assertNoContent();

    $this->assertDatabaseCount('clients', 2)
        ->assertDatabaseHas('orders', [
            'id' => $order->id,
            'client_id' => $newClient->id,
        ]);
});

it('creates a client when the phone number is unknown', function() {
    fakeGeocoding();
    createCompletedOrderStatus();
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'phoneNumber' => '+48 999 888 777',
        ]))
        ->assertNoContent();

    $this->assertDatabaseCount('clients', 2)
        ->assertDatabaseHas('orders', [
            'id' => $order->id,
            'client_id' => Client::where('phone_number', '+48 999 888 777')->sole()->id,
        ]);
});

it('completes the order when the status changes to completed', function() {
    Event::fake([OrderCompleted::class]);
    fakeGeocoding();
    $completedStatus = createCompletedOrderStatus();
    $order = Order::factory()->create();

    $this->travelTo(Carbon::parse('2026-05-20 10:00:00'));

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'statusId' => $completedStatus->id,
        ]))
        ->assertNoContent();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status_id' => $completedStatus->id,
        'date_completed' => '2026-05-20 10:00:00',
    ]);

    Event::assertDispatched(OrderCompleted::class, fn(OrderCompleted $event) => $event->order->is($order));
});

it('uses the given completion date', function() {
    fakeGeocoding();
    $completedStatus = createCompletedOrderStatus();
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'statusId' => $completedStatus->id,
            'dateCompleted' => '2026-05-18',
        ]))
        ->assertNoContent();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'date_completed' => '2026-05-18 00:00:00',
    ]);
});

it('notifies the users subscribed to the order completed event, except the one updating the order', function() {
    Notification::fake();
    fakeGeocoding();
    $completedStatus = createCompletedOrderStatus();
    $order = Order::factory()->create();

    $event = NotificationEvent::factory()->create(['symbol' => NotificationEventType::ORDER_COMPLETED->value]);
    $channel = NotificationChannel::factory()->create(['symbol' => NotificationChannelType::MAIL->value]);

    $subscribedUser = User::factory()->create();
    $subscribedUser->notificationSettings()->create([
        'notification_event_id' => $event->id,
        'notification_channel_id' => $channel->id,
    ]);

    $actingUser = User::factory()->create();
    $actingUser->notificationSettings()->create([
        'notification_event_id' => $event->id,
        'notification_channel_id' => $channel->id,
    ]);

    $unsubscribedUser = User::factory()->create();

    actingAsUser($actingUser, PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'statusId' => $completedStatus->id,
        ]))
        ->assertNoContent();

    Notification::assertSentTo($subscribedUser, OrderCompletedNotification::class);
    Notification::assertNotSentTo($actingUser, OrderCompletedNotification::class);
    Notification::assertNotSentTo($unsubscribedUser, OrderCompletedNotification::class);
});

it('does not dispatch the order completed event when the order was already completed', function() {
    Event::fake([OrderCompleted::class]);
    fakeGeocoding();
    $completedStatus = createCompletedOrderStatus();
    $order = Order::factory()->create([
        'status_id' => $completedStatus->id,
        'date_completed' => '2026-05-18 10:00:00',
    ]);

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'dateCompleted' => '2026-05-18 10:00:00',
        ]))
        ->assertNoContent();

    Event::assertNotDispatched(OrderCompleted::class);
});

it('clears the completion date when the status is not completed and no date is given', function() {
    Event::fake([OrderCompleted::class]);
    fakeGeocoding();
    $completedStatus = createCompletedOrderStatus();
    $order = Order::factory()->create([
        'status_id' => $completedStatus->id,
        'date_completed' => '2026-05-18 10:00:00',
    ]);
    $inProgressStatus = Order::factory()->create()->status;

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'statusId' => $inProgressStatus->id,
        ]))
        ->assertNoContent();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status_id' => $inProgressStatus->id,
        'date_completed' => null,
    ]);

    Event::assertNotDispatched(OrderCompleted::class);
});

it('returns an error when the order does not exist', function() {
    fakeGeocoding();
    createCompletedOrderStatus();
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'id' => $order->id + 1000,
        ]))
        ->assertNotFound();

    $this->assertDatabaseMissing('order_translations', ['remarks' => 'Updated remarks']);
});

it('does not update the order when the validation fails', function() {
    fakeGeocoding();
    createCompletedOrderStatus();
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_UPDATE)
        ->putJson('/api/orders', updateOrderPayload($order, [
            'dateDeadline' => '2026-05-14',
        ]))
        ->assertUnprocessable();

    $this->assertDatabaseMissing('order_translations', ['remarks' => 'Updated remarks']);
});

it('has proper validation rules', function() {
    expect(new OrderRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    $order = Order::factory()->create();

    actingAsApiUser()
        ->putJson('/api/orders', updateOrderPayload($order))
        ->assertForbidden();

    $this->assertDatabaseMissing('order_translations', ['remarks' => 'Updated remarks']);
});

it('returns an error on unauthorized request', function() {
    $order = Order::factory()->create();

    $this->putJson('/api/orders', updateOrderPayload($order))
        ->assertUnauthorized();

    $this->assertDatabaseMissing('order_translations', ['remarks' => 'Updated remarks']);
});
