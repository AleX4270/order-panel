<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Http\Requests\Api\OrderRequest\OrderRequestDeleteRequest;
use App\Models\OrderRequest;

it('deletes the requested order request', function() {
    $orderRequest = OrderRequest::factory()->create();

    $response = actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->deleteJson('/api/order-requests/'.$orderRequest->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.deleted'));

    $this->assertSoftDeleted('order_requests', ['id' => $orderRequest->id]);
});

it('keeps the client and the address of the deleted order request', function() {
    $orderRequest = OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->deleteJson('/api/order-requests/'.$orderRequest->id)
        ->assertOk();

    $this->assertDatabaseHas('clients', ['id' => $orderRequest->client_id])
        ->assertDatabaseHas('addresses', ['id' => $orderRequest->address_id]);
});

it('does not touch the other order requests', function() {
    $orderRequest = OrderRequest::factory()->create();
    $untouchedOrderRequest = OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->deleteJson('/api/order-requests/'.$orderRequest->id)
        ->assertOk();

    $this->assertDatabaseHas('order_requests', [
        'id' => $untouchedOrderRequest->id,
        'deleted_at' => null,
    ]);
});

it('returns an error when the order request does not exist', function() {
    $orderRequest = OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->deleteJson('/api/order-requests/'.($orderRequest->id + 1000))
        ->assertNotFound();

    $this->assertDatabaseHas('order_requests', [
        'id' => $orderRequest->id,
        'deleted_at' => null,
    ]);
});

it('returns an error when the order request has already been deleted', function() {
    $orderRequest = OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->deleteJson('/api/order-requests/'.$orderRequest->id)
        ->assertOk();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->deleteJson('/api/order-requests/'.$orderRequest->id)
        ->assertNotFound();
});

it('returns an error for an id that is not a positive integer', function(string $id) {
    $orderRequest = OrderRequest::factory()->create();

    actingAsApiUser(PermissionType::ORDER_REQUESTS_MANAGE)
        ->deleteJson('/api/order-requests/'.$id)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('id');

    $this->assertDatabaseHas('order_requests', [
        'id' => $orderRequest->id,
        'deleted_at' => null,
    ]);
})->with([
    'not a number' => ['id' => 'abc'],
    'zero' => ['id' => '0'],
    'negative' => ['id' => '-3'],
    'decimal' => ['id' => '1.5'],
]);

it('has proper validation rules', function() {
    expect(new OrderRequestDeleteRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    $orderRequest = OrderRequest::factory()->create();

    actingAsApiUser()
        ->deleteJson('/api/order-requests/'.$orderRequest->id)
        ->assertForbidden();

    $this->assertDatabaseHas('order_requests', [
        'id' => $orderRequest->id,
        'deleted_at' => null,
    ]);
});

it('returns an error on unauthorized request', function() {
    $orderRequest = OrderRequest::factory()->create();

    $this->deleteJson('/api/order-requests/'.$orderRequest->id)
        ->assertUnauthorized();

    $this->assertDatabaseHas('order_requests', [
        'id' => $orderRequest->id,
        'deleted_at' => null,
    ]);
});
