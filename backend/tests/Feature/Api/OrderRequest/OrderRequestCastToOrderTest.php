<?php
declare(strict_types=1);

use App\Enums\OrderStatusType;
use App\Enums\PermissionType;
use App\Enums\PriorityType;
use App\Http\Requests\Api\OrderRequest\OrderRequestCastToOrderRequest;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderRequest;
use App\Models\OrderStatus;
use App\Models\Priority;
use App\Models\User;
use Illuminate\Support\Carbon;

function createOrderDictionaries(): array {
    return [
        Priority::factory()->create(['symbol' => PriorityType::STANDARD->value]),
        OrderStatus::factory()->create(['symbol' => OrderStatusType::IN_PROGRESS->value]),
    ];
}

it('creates an order from the order request', function() {
    [$priority, $status] = createOrderDictionaries();
    $orderRequest = OrderRequest::factory()->create();
    $actingUser = User::factory()->create();

    $response = actingAsUser($actingUser, PermissionType::ORDER_REQUESTS_MANAGE)
        ->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id]);

    $response
        ->assertCreated()
        ->assertJsonPath('message', __('response.created'));

    $this->assertDatabaseCount('orders', 1)
        ->assertDatabaseHas('orders', [
            'client_id' => $orderRequest->client_id,
            'address_id' => $orderRequest->address_id,
            'priority_id' => $priority->id,
            'status_id' => $status->id,
            'user_creation_id' => $actingUser->id,
            'user_modification_id' => $actingUser->id,
            'is_active' => true,
            'date_completed' => null,
        ]);
});

it('sets the deadline two weeks after the creation', function() {
    createOrderDictionaries();
    $orderRequest = OrderRequest::factory()->create();

    $this->travelTo(Carbon::parse('2026-05-15 10:00:00'));

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id])
        ->assertCreated();

    $this->assertDatabaseHas('orders', [
        'created_at' => '2026-05-15 10:00:00',
        'date_deadline' => '2026-05-29 10:00:00',
    ]);
});

it('generates a symbol for the order', function() {
    createOrderDictionaries();
    $orderRequest = OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id])
        ->assertCreated();

    expect(Order::sole()->symbol)->toBeString()->toHaveLength(16);
});

it('copies the remarks to the order translation in the current locale', function() {
    createOrderDictionaries();
    $orderRequest = OrderRequest::factory()->create(['remarks' => 'Call before arrival']);

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id])
        ->assertCreated();

    $this->assertDatabaseCount('order_translations', 1)
        ->assertDatabaseHas('order_translations', [
            'order_id' => Order::sole()->id,
            'language_id' => Language::where('symbol', app()->getLocale())->sole()->id,
            'remarks' => 'Call before arrival',
        ]);
});

it('removes the casted order request but keeps its client and address', function() {
    createOrderDictionaries();
    $orderRequest = OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id])
        ->assertCreated();

    $this->assertSoftDeleted('order_requests', ['id' => $orderRequest->id])
        ->assertDatabaseHas('clients', ['id' => $orderRequest->client_id])
        ->assertDatabaseHas('addresses', ['id' => $orderRequest->address_id]);
});

it('does not touch the other order requests', function() {
    createOrderDictionaries();
    $orderRequest = OrderRequest::factory()->create();
    $untouchedOrderRequest = OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id])
        ->assertCreated();

    $this->assertDatabaseCount('orders', 1)
        ->assertDatabaseHas('order_requests', [
            'id' => $untouchedOrderRequest->id,
            'deleted_at' => null,
        ]);
});

it('returns an error when the order request does not exist', function() {
    createOrderDictionaries();
    $orderRequest = OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id + 1000])
        ->assertNotFound();

    $this->assertDatabaseCount('orders', 0);
});

it('returns an error when the order request has already been casted', function() {
    createOrderDictionaries();
    $orderRequest = OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id])
        ->assertCreated();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id])
        ->assertNotFound();

    $this->assertDatabaseCount('orders', 1);
});

it('returns an error for a missing id or an id that is not a positive integer', function(array $payload) {
    createOrderDictionaries();
    OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->postJson('/api/order-requests/cast-to-order', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('id');

    $this->assertDatabaseCount('orders', 0);
})->with([
    'missing' => ['payload' => []],
    'not a number' => ['payload' => ['id' => 'abc']],
    'zero' => ['payload' => ['id' => '0']],
    'negative' => ['payload' => ['id' => '-3']],
    'decimal' => ['payload' => ['id' => '1.5']],
]);

it('has proper validation rules', function() {
    expect(new OrderRequestCastToOrderRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    createOrderDictionaries();
    $orderRequest = OrderRequest::factory()->create();

    actingAsApiUser()
        ->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id])
        ->assertForbidden();

    $this->assertDatabaseCount('orders', 0)
        ->assertDatabaseHas('order_requests', [
            'id' => $orderRequest->id,
            'deleted_at' => null,
        ]);
});

it('returns an error on unauthorized request', function() {
    createOrderDictionaries();
    $orderRequest = OrderRequest::factory()->create();

    $this->postJson('/api/order-requests/cast-to-order', ['id' => $orderRequest->id])
        ->assertUnauthorized();

    $this->assertDatabaseCount('orders', 0);
});
