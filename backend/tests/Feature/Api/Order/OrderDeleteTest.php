<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Http\Requests\Api\Order\OrderDeleteRequest;
use App\Models\Order;

it('deactivates the requested order', function() {
    $order = Order::factory()->create();

    $response = actingAsApiUser(PermissionType::ORDERS_DELETE)
        ->deleteJson('/api/orders/'.$order->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.deleted'));

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'is_active' => false,
    ]);
});

it('keeps the deleted order in the database', function() {
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_DELETE)
        ->deleteJson('/api/orders/'.$order->id)
        ->assertOk();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'symbol' => $order->symbol,
    ])
        ->assertDatabaseHas('order_translations', ['order_id' => $order->id])
        ->assertDatabaseHas('clients', ['id' => $order->client_id])
        ->assertDatabaseHas('addresses', ['id' => $order->address_id]);
});

it('does not touch the other orders', function() {
    $order = Order::factory()->create();
    $untouchedOrder = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_DELETE)
        ->deleteJson('/api/orders/'.$order->id)
        ->assertOk();

    $this->assertDatabaseHas('orders', [
        'id' => $untouchedOrder->id,
        'is_active' => true,
    ]);
});

it('deactivates an already deleted order without an error', function() {
    $order = Order::factory()->inactive()->create();

    actingAsApiUser(PermissionType::ORDERS_DELETE)
        ->deleteJson('/api/orders/'.$order->id)
        ->assertOk()
        ->assertJsonPath('message', __('response.deleted'));

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'is_active' => false,
    ]);
});

it('returns an error when the order does not exist', function() {
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_DELETE)
        ->deleteJson('/api/orders/'.($order->id + 1000))
        ->assertNotFound();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'is_active' => true,
    ]);
});

it('returns an error for an id that is not a positive integer', function(string $id) {
    $order = Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_DELETE)
        ->deleteJson('/api/orders/'.$id)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('id');

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'is_active' => true,
    ]);
})->with([
    'not a number' => ['id' => 'abc'],
    'zero' => ['id' => '0'],
    'negative' => ['id' => '-3'],
    'decimal' => ['id' => '1.5'],
]);

it('has proper validation rules', function() {
    expect(new OrderDeleteRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    $order = Order::factory()->create();

    actingAsApiUser()
        ->deleteJson('/api/orders/'.$order->id)
        ->assertForbidden();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'is_active' => true,
    ]);
});

it('returns an error on unauthorized request', function() {
    $order = Order::factory()->create();

    $this->deleteJson('/api/orders/'.$order->id)
        ->assertUnauthorized();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'is_active' => true,
    ]);
});
