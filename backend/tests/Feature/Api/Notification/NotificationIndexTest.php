<?php
declare(strict_types=1);

use App\Http\Requests\Api\Notification\NotificationFilterRequest;
use App\Models\User;
use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns all notifications of the authenticated user', function() {
    $user = User::factory()->create();
    $secondUser = User::factory()->create();

    $notifications = NotificationFactory::new()
        ->count(5)
        ->for($user, 'notifiable')
        ->read()
        ->create();

    NotificationFactory::new()
        ->count(15)
        ->for($secondUser, 'notifiable')
        ->create();

    $response = actingAsUser($user)
        ->getJson('/api/notifications');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('data.*.id', $notifications->pluck('id')->all())
        ->assertJsonPath('data.*.title', $notifications->pluck('data.title')->all())
        ->assertJsonPath('data.*.message', $notifications->pluck('data.message')->all())
        ->assertJsonPath('data.*.readAt', $notifications->pluck('read_at')->map(fn(?Carbon $date) => $date?->toJSON())->all())
        ->assertJsonPath('data.*.createdAt', $notifications->pluck('data.createdAt')->all())
        ->assertJsonPath('data.*.type', $notifications->pluck('type')->all());
});

it('returns only unread notifications of the authenticated user', function() {
    $user = User::factory()->create();

    NotificationFactory::new()
        ->count(5)
        ->for($user, 'notifiable')
        ->read()
        ->create();

    $notifications = NotificationFactory::new()
        ->count(3)
        ->for($user, 'notifiable')
        ->create();

    $response = actingAsUser($user)
        ->getJson('/api/notifications?onlyUnread=true');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.*.id', $notifications->pluck('id')->all())
        ->assertJsonPath('data.*.title', $notifications->pluck('data.title')->all())
        ->assertJsonPath('data.*.message', $notifications->pluck('data.message')->all())
        ->assertJsonPath('data.*.readAt', $notifications->pluck('read_at')->all())
        ->assertJsonPath('data.*.createdAt', $notifications->pluck('data.createdAt')->all())
        ->assertJsonPath('data.*.type', $notifications->pluck('type')->all());
});

it('returns notifications from latest to oldest', function() {
    $user = User::factory()->create();

    $notifications = NotificationFactory::new()
        ->count(5)
        ->for($user, 'notifiable')
        ->state(new Sequence(fn(Sequence $sequence) => [
            'created_at' => Carbon::now()->subDays($sequence->index),
        ]))
        ->create();

    $response = actingAsUser($user)
        ->getJson('/api/notifications');

    $expectedIdsOrder = $notifications->sortByDesc('created_at')->pluck('id')->values()->all();

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('data.*.id', $expectedIdsOrder);
});

it('responds with valid api data structure', function() {
    $user = User::factory()->create();

    NotificationFactory::new()
        ->count(3)
        ->for($user, 'notifiable')
        ->create();

    NotificationFactory::new()
        ->for($user, 'notifiable')
        ->read()
        ->create();

    $response = actingAsUser($user)
        ->getJson('/api/notifications');

    $response
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) =>
            $json->has('timestamp')
                ->has('message')
                ->has('data', 4, fn(AssertableJson $json) =>
                    $json->whereType('id', 'string')
                        ->whereType('title', 'string')
                        ->whereType('message', 'string')
                        ->whereType('readAt', 'string|null')
                        ->whereType('createdAt', 'string')
                        ->whereType('type', 'string')
                )
        );
});

it('has proper validation rules', function() {
    expect(new NotificationFilterRequest()->rules())->toMatchSnapshot();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/notifications')
        ->assertUnauthorized();
});
