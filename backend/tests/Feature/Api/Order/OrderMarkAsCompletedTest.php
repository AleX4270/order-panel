<?php
declare(strict_types=1);

use App\Enums\NotificationChannelType;
use App\Enums\NotificationEventType;
use App\Enums\PermissionType;
use App\Events\OrderCompleted;
use App\Http\Requests\Api\Order\OrderMarkAsCompletedRequest;
use App\Models\NotificationChannel;
use App\Models\NotificationEvent;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderCompletedNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

it('marks the requested order as completed', function() {
    $completedStatus = createCompletedOrderStatus();
    $order = Order::factory()->create();
    $actingUser = User::factory()->create();

    $this->travelTo(Carbon::parse('2026-05-20 10:00:00'));

    actingAsUser($actingUser, PermissionType::ORDERS_MARK_AS_COMPLETED)
        ->postJson('/api/orders/mark-as-completed', ['id' => $order->id])
        ->assertNoContent();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status_id' => $completedStatus->id,
        'date_completed' => '2026-05-20 10:00:00',
        'user_modification_id' => $actingUser->id,
        'user_creation_id' => $order->user_creation_id,
    ]);
});

it('does not touch the other orders', function() {
    createCompletedOrderStatus();
    $order = Order::factory()->create();
    $untouchedOrder = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_MARK_AS_COMPLETED)
        ->postJson('/api/orders/mark-as-completed', ['id' => $order->id])
        ->assertNoContent();

    $this->assertDatabaseHas('orders', [
        'id' => $untouchedOrder->id,
        'status_id' => $untouchedOrder->status_id,
        'date_completed' => null,
    ]);
});

it('dispatches the order completed event', function() {
    Event::fake([OrderCompleted::class]);
    createCompletedOrderStatus();
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_MARK_AS_COMPLETED)
        ->postJson('/api/orders/mark-as-completed', ['id' => $order->id])
        ->assertNoContent();

    Event::assertDispatched(OrderCompleted::class, fn(OrderCompleted $event) => $event->order->is($order));
});

it('notifies the users subscribed to the order completed event, except the one completing the order', function() {
    Notification::fake();
    createCompletedOrderStatus();
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

    actingAsUser($actingUser, PermissionType::ORDERS_MARK_AS_COMPLETED)
        ->postJson('/api/orders/mark-as-completed', ['id' => $order->id])
        ->assertNoContent();

    Notification::assertSentTo($subscribedUser, OrderCompletedNotification::class);
    Notification::assertNotSentTo($actingUser, OrderCompletedNotification::class);
    Notification::assertNotSentTo($unsubscribedUser, OrderCompletedNotification::class);
});

it('completes an order having the completed status but no completion date', function() {
    Event::fake([OrderCompleted::class]);
    $completedStatus = createCompletedOrderStatus();
    $order = Order::factory()->create([
        'status_id' => $completedStatus->id,
        'date_completed' => null,
    ]);

    $this->travelTo(Carbon::parse('2026-05-20 10:00:00'));

    actingAsApiUser(PermissionType::ORDERS_MARK_AS_COMPLETED)
        ->postJson('/api/orders/mark-as-completed', ['id' => $order->id])
        ->assertNoContent();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'date_completed' => '2026-05-20 10:00:00',
    ]);

    Event::assertDispatched(OrderCompleted::class);
});

it('returns an error when the order is already completed', function() {
    Event::fake([OrderCompleted::class]);
    $completedStatus = createCompletedOrderStatus();
    $order = Order::factory()->create([
        'status_id' => $completedStatus->id,
        'date_completed' => '2026-05-18 10:00:00',
    ]);

    actingAsApiUser(PermissionType::ORDERS_MARK_AS_COMPLETED)
        ->postJson('/api/orders/mark-as-completed', ['id' => $order->id])
        ->assertBadRequest()
        ->assertJsonPath('message', __('response.orderAlreadyCompleted'));

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'date_completed' => '2026-05-18 10:00:00',
        'user_modification_id' => $order->user_modification_id,
    ]);

    Event::assertNotDispatched(OrderCompleted::class);
});

it('returns an error when the completed status is not configured', function() {
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_MARK_AS_COMPLETED)
        ->postJson('/api/orders/mark-as-completed', ['id' => $order->id])
        ->assertInternalServerError()
        ->assertJsonPath('message', __('response.internalError'));

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status_id' => $order->status_id,
        'date_completed' => null,
    ]);
});

it('returns an error when the order does not exist', function() {
    createCompletedOrderStatus();
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_MARK_AS_COMPLETED)
        ->postJson('/api/orders/mark-as-completed', ['id' => $order->id + 1000])
        ->assertNotFound();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'date_completed' => null,
    ]);
});

it('returns an error for a missing id or an id that is not a positive integer', function(array $payload) {
    createCompletedOrderStatus();
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_MARK_AS_COMPLETED)
        ->postJson('/api/orders/mark-as-completed', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('id');

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'date_completed' => null,
    ]);
})->with([
    'missing' => ['payload' => []],
    'not a number' => ['payload' => ['id' => 'abc']],
    'zero' => ['payload' => ['id' => '0']],
    'negative' => ['payload' => ['id' => '-3']],
    'decimal' => ['payload' => ['id' => '1.5']],
]);

it('has proper validation rules', function() {
    expect(new OrderMarkAsCompletedRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    createCompletedOrderStatus();
    $order = Order::factory()->create();

    actingAsApiUser()
        ->postJson('/api/orders/mark-as-completed', ['id' => $order->id])
        ->assertForbidden();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'date_completed' => null,
    ]);
});

it('returns an error on unauthorized request', function() {
    createCompletedOrderStatus();
    $order = Order::factory()->create();

    $this->postJson('/api/orders/mark-as-completed', ['id' => $order->id])
        ->assertUnauthorized();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'date_completed' => null,
    ]);
});
