<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Http\Requests\Api\User\UserShowRequest;
use App\Models\Language;
use App\Models\NotificationChannel;
use App\Models\NotificationEvent;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNotificationSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function() {
    User::query()->delete();
});

function assignNotificationSettings(User $user, NotificationEvent $event, iterable $channels): void {
    foreach($channels as $channel) {
        UserNotificationSetting::create([
            'user_id' => $user->id,
            'notification_event_id' => $event->id,
            'notification_channel_id' => $channel->id,
        ]);
    }
}

it('returns the requested user', function() {
    $user = User::factory()->create([
        'name' => 'msmith',
        'first_name' => 'Maria',
        'last_name' => 'Smith',
        'email' => 'm.smith@system.local',
    ]);

    $response = actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$user->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.name', 'msmith')
        ->assertJsonPath('data.firstName', 'Maria')
        ->assertJsonPath('data.lastName', 'Smith')
        ->assertJsonPath('data.email', 'm.smith@system.local')
        ->assertJsonPath('data.isInternal', false)
        ->assertJsonPath('data.roles', [])
        ->assertJsonPath('data.notificationSettings', []);
});

it('returns an error when the user does not exist', function() {
    $user = User::factory()->create();

    actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.($user->id + 1000))
        ->assertNotFound();
});

it('returns an error for an id that is not a positive integer', function(string $id) {
    $user = User::factory()->create();

    actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$id)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('id');
})->with([
    'not a number' => ['id' => 'abc'],
    'zero' => ['id' => '0'],
    'negative' => ['id' => '-3'],
    'decimal' => ['id' => '1.5'],
]);

it('returns the roles assigned to the user', function() {
    $roles = Role::factory()->count(2)->create();
    $otherRole = Role::factory()->create();

    $user = User::factory()->create();
    $roles->each(fn(Role $role) => $user->assignRole($role));

    $response = actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$user->id);

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data.roles')
        ->assertJsonPath('data.roles.*.id', $roles->pluck('id')->all())
        ->assertJsonPath('data.roles.*.symbol', $roles->pluck('name')->all())
        ->assertJsonPath('data.roles.*.name', $roles->pluck('translations.0.name')->all())
        ->assertJsonMissing(['symbol' => $otherRole->name]);
});

it('returns role names translated to the current locale', function(string $locale, string $expectedName) {
    $role = Role::factory()->create();
    $role->translations()->delete();
    $role->translations()->createMany([
        [
            'language_id' => Language::where('symbol', 'pl')->sole()->id,
            'name' => 'Kierownik',
        ],
        [
            'language_id' => Language::where('symbol', 'en')->sole()->id,
            'name' => 'Manager',
        ],
    ]);

    $user = User::factory()->create();
    $user->assignRole($role);

    App::setLocale($locale);

    $response = actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$user->id);

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data.roles')
        ->assertJsonPath('data.roles.0.symbol', $role->name)
        ->assertJsonPath('data.roles.0.name', $expectedName);
})->with([
    'polish' => ['locale' => 'pl', 'expectedName' => 'Kierownik'],
    'english' => ['locale' => 'en', 'expectedName' => 'Manager'],
]);

it('returns a role without a name when it has no translation for the current locale', function() {
    $role = Role::factory()->create();
    $role->translations()->delete();

    $user = User::factory()->create();
    $user->assignRole($role);

    $response = actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$user->id);

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data.roles')
        ->assertJsonPath('data.roles.0.symbol', $role->name)
        ->assertJsonPath('data.roles.0.name', null);
});

it('returns notification settings grouped by event', function() {
    $events = NotificationEvent::factory()->count(2)->create();
    $channels = NotificationChannel::factory()->count(3)->create();

    $user = User::factory()->create();
    assignNotificationSettings($user, $events->first(), $channels->take(2));
    assignNotificationSettings($user, $events->last(), $channels->skip(2));

    $response = actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$user->id);

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data.notificationSettings')
        ->assertJsonPath('data.notificationSettings.*.eventId', $events->pluck('id')->all())
        ->assertJsonPath('data.notificationSettings.0.channelIds', $channels->take(2)->pluck('id')->all())
        ->assertJsonPath('data.notificationSettings.1.channelIds', $channels->skip(2)->pluck('id')->values()->all());
});

it('returns only the notification settings belonging to the requested user', function() {
    $event = NotificationEvent::factory()->create();
    $channels = NotificationChannel::factory()->count(2)->create();

    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    assignNotificationSettings($user, $event, $channels->take(1));
    assignNotificationSettings($otherUser, $event, $channels);

    $response = actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$user->id);

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data.notificationSettings')
        ->assertJsonPath('data.notificationSettings.0.eventId', $event->id)
        ->assertJsonPath('data.notificationSettings.0.channelIds', [$channels->first()->id]);
});

it('returns another user than the one making the request', function() {
    $requestingUser = User::factory()->create();
    $requestedUser = User::factory()->create(['name' => 'requested']);

    $response = actingAsUser($requestingUser, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$requestedUser->id);

    $response
        ->assertOk()
        ->assertJsonPath('data.id', $requestedUser->id)
        ->assertJsonPath('data.name', 'requested');
});

it('returns a user that is no longer active, unlike the index endpoint', function() {
    $viewer = User::factory()->create();
    $inactiveUser = User::factory()->inactive()->create();

    actingAsUser($viewer, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$inactiveUser->id)
        ->assertOk()
        ->assertJsonPath('data.id', $inactiveUser->id);
});

it('returns an internal user marked as internal', function() {
    $user = User::factory()->create(['is_internal' => true]);

    actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$user->id)
        ->assertOk()
        ->assertJsonPath('data.isInternal', true);
});

it('returns a user missing a first and last name', function() {
    $user = User::factory()->create([
        'first_name' => null,
        'last_name' => null,
    ]);

    actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$user->id)
        ->assertOk()
        ->assertJsonPath('data.firstName', null)
        ->assertJsonPath('data.lastName', null);
});

it('does not expose sensitive user fields', function() {
    $user = User::factory()->create();

    $response = actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$user->id);

    $response
        ->assertOk()
        ->assertJsonMissingPath('data.password')
        ->assertJsonMissingPath('data.remember_token')
        ->assertJsonMissingPath('data.email_verified_at')
        ->assertJsonMissingPath('data.is_active');
});

it('responds with valid api data structure', function() {
    $role = Role::factory()->create();
    $event = NotificationEvent::factory()->create();
    $channels = NotificationChannel::factory()->count(2)->create();

    $user = User::factory()->create([
        'first_name' => 'Maria',
        'last_name' => 'Smith',
    ]);
    $user->assignRole($role);
    assignNotificationSettings($user, $event, $channels);

    $response = actingAsUser($user, PermissionType::USERS_SHOW)
        ->getJson('/api/users/'.$user->id);

    $response
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) =>
            $json->has('timestamp')
                ->has('message')
                ->has('data', fn(AssertableJson $json) =>
                    $json->whereType('id', 'integer')
                        ->whereType('name', 'string')
                        ->whereType('firstName', 'string|null')
                        ->whereType('lastName', 'string|null')
                        ->whereType('email', 'string')
                        ->whereType('dateCreated', 'string')
                        ->whereType('dateUpdated', 'string')
                        ->whereType('isInternal', 'boolean')
                        ->has('roles', fn(AssertableJson $json) =>
                            $json->each(fn(AssertableJson $json) =>
                                $json->whereType('id', 'integer')
                                    ->whereType('symbol', 'string')
                                    ->whereType('name', 'string|null')
                            )
                        )
                        ->has('notificationSettings', fn(AssertableJson $json) =>
                            $json->each(fn(AssertableJson $json) =>
                                $json->whereType('eventId', 'integer')
                                    ->whereType('channelIds', 'array')
                            )
                        )
                )
        );
});

it('has proper validation rules', function() {
    expect(new UserShowRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    $response = actingAsApiUser()
        ->getJson('api/users/1');

    $response
        ->assertForbidden();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/users/1')
        ->assertUnauthorized();
});
