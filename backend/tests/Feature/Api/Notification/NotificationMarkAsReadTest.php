<?php
declare(strict_types=1);

use App\Http\Requests\Api\Notification\NotificationMarkAsReadRequest;
use App\Models\User;
use Database\Factories\NotificationFactory;
use Illuminate\Testing\Fluent\AssertableJson;

it('marks the notification as read for authenticated user', function() {
    $user = User::factory()->create();
    $secondUser = User::factory()->create();

    $notification = NotificationFactory::new()
        ->for($user, 'notifiable')
        ->create();

    NotificationFactory::new()
        ->for($secondUser, 'notifiable')
        ->create();

    $response = actingAsUser($user)
        ->postJson("/api/notifications/mark-as-read?id={$notification->id}");

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'));

    expect($notification->fresh()->read_at)->not()->toBeNull();
});

it('returns an error for non existent notification id', function() {
    $user = User::factory()->create();

    $uuid = fake()->uuid();

    $response = actingAsUser($user)
        ->postJson("/api/notifications/mark-as-read?id={$uuid}");

    $response
        ->assertNotFound()
        ->assertJsonPath('message', __('response.notFound'));
});

it('returns an error on attempt to mark other user\'s notification as read', function() {
    $user = User::factory()->create();
    $secondUser = User::factory()->create();

    NotificationFactory::new()
        ->for($user, 'notifiable')
        ->read()
        ->create();

    $otherNotification = NotificationFactory::new()
        ->for($secondUser, 'notifiable')
        ->create();

    $response = actingAsUser($user)
        ->postJson("/api/notifications/mark-as-read?id={$otherNotification->id}");

    $response
        ->assertNotFound()
        ->assertJsonPath('message', __('response.notFound'));

    expect($otherNotification->fresh()->read_at)->toBeNull();
});

it('responds with valid api data structure', function() {
    $user = User::factory()->create();

    $notification = NotificationFactory::new()
        ->for($user, 'notifiable')
        ->create();

    $response = actingAsUser($user)
        ->postJson("/api/notifications/mark-as-read?id={$notification->id}");

    $response
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) =>
            $json->has('timestamp')
                ->has('message')
                ->whereType('data', 'null')
        );
});

it('has proper validation rules', function() {
    expect(new NotificationMarkAsReadRequest()->rules())->toMatchSnapshot();
});

it('returns an error on unauthorized request', function() {
    $this->postJson('/api/notifications/mark-as-read')
        ->assertUnauthorized();
});