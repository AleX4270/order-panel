<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Http\Requests\Api\User\UserRequest;
use App\Models\NotificationChannel;
use App\Models\NotificationEvent;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function() {
    User::query()->delete();
});

function storeUserPayload(array $overrides = []): array {
    return array_merge([
        'firstName' => 'Maria',
        'lastName' => 'Smith',
        'username' => 'msmith',
        'email' => 'm.smith@system.local',
        'password' => 'Str0ng!Pass',
        'passwordConfirmed' => 'Str0ng!Pass',
        'notificationSettings' => [],
    ], $overrides);
}

it('creates a new user', function() {
    $response = actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload());

    $response
        ->assertCreated()
        ->assertJsonPath('message', __('response.created'));

    $this->assertDatabaseHas('users', [
        'name' => 'msmith',
        'first_name' => 'Maria',
        'last_name' => 'Smith',
        'email' => 'm.smith@system.local',
    ]);
});

it('creates the user as active and not internal', function() {
    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload())
        ->assertCreated();

    $this->assertDatabaseHas('users', [
        'name' => 'msmith',
        'is_active' => true,
        'is_internal' => false,
    ]);
});

it('stores the password hashed', function() {
    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload())
        ->assertCreated();

    $user = User::where('name', 'msmith')->sole();

    expect($user->password)->not->toBe('Str0ng!Pass')
        ->and(Hash::check('Str0ng!Pass', $user->password))->toBeTrue();
});

it('creates a user without the first and last name', function() {
    $payload = storeUserPayload();
    unset($payload['firstName'], $payload['lastName']);

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', $payload)
        ->assertCreated();

    $this->assertDatabaseHas('users', [
        'name' => 'msmith',
        'first_name' => null,
        'last_name' => null,
    ]);
});

it('assigns the given roles', function() {
    $roles = Role::factory()->count(2)->create();
    $otherRole = Role::factory()->create();

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload([
            'roles' => $roles->pluck('name')->all(),
        ]))
        ->assertCreated();

    $user = User::where('name', 'msmith')->sole();

    $this->assertDatabaseHas('model_has_roles', [
        'role_id' => $roles->first()->id,
        'model_id' => $user->id,
    ])
        ->assertDatabaseHas('model_has_roles', [
            'role_id' => $roles->last()->id,
            'model_id' => $user->id,
        ])
        ->assertDatabaseMissing('model_has_roles', [
            'role_id' => $otherRole->id,
            'model_id' => $user->id,
        ]);
});

it('creates a user without any role', function() {
    Role::factory()->create();

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload())
        ->assertCreated();

    $user = User::where('name', 'msmith')->sole();

    $this->assertDatabaseMissing('model_has_roles', [
        'model_id' => $user->id,
    ]);
});

it('creates the notification settings', function() {
    $events = NotificationEvent::factory()->count(2)->create();
    $channels = NotificationChannel::factory()->count(2)->create();

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload([
            'notificationSettings' => [
                [
                    'eventId' => $events->first()->id,
                    'channelIds' => $channels->pluck('id')->all(),
                ],
                [
                    'eventId' => $events->last()->id,
                    'channelIds' => [$channels->first()->id],
                ],
            ],
        ]))
        ->assertCreated();

    $user = User::where('name', 'msmith')->sole();

    $this->assertDatabaseCount('user_notification_settings', 3)
        ->assertDatabaseHas('user_notification_settings', [
            'user_id' => $user->id,
            'notification_event_id' => $events->first()->id,
            'notification_channel_id' => $channels->last()->id,
        ])
        ->assertDatabaseHas('user_notification_settings', [
            'user_id' => $user->id,
            'notification_event_id' => $events->last()->id,
            'notification_channel_id' => $channels->first()->id,
        ]);
});

it('skips the notification settings without any channel', function() {
    $events = NotificationEvent::factory()->count(2)->create();
    $channel = NotificationChannel::factory()->create();

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload([
            'notificationSettings' => [
                [
                    'eventId' => $events->first()->id,
                    'channelIds' => [$channel->id],
                ],
                [
                    'eventId' => $events->last()->id,
                    'channelIds' => [],
                ],
            ],
        ]))
        ->assertCreated();

    $user = User::where('name', 'msmith')->sole();

    $this->assertDatabaseCount('user_notification_settings', 1)
        ->assertDatabaseHas('user_notification_settings', [
            'user_id' => $user->id,
            'notification_event_id' => $events->first()->id,
            'notification_channel_id' => $channel->id,
        ]);
});

it('creates a user without the notification settings', function() {
    $payload = storeUserPayload();
    unset($payload['notificationSettings']);

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', $payload)
        ->assertCreated();

    $user = User::where('name', 'msmith')->sole();

    $this->assertDatabaseMissing('user_notification_settings', [
        'user_id' => $user->id,
    ]);
});

it('does not touch the notification settings of the other users', function() {
    $event = NotificationEvent::factory()->create();
    $channel = NotificationChannel::factory()->create();

    $otherUser = User::factory()->create();
    $otherUser->notificationSettings()->create([
        'notification_event_id' => $event->id,
        'notification_channel_id' => $channel->id,
    ]);

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload())
        ->assertCreated();

    $this->assertDatabaseHas('user_notification_settings', [
        'user_id' => $otherUser->id,
        'notification_event_id' => $event->id,
        'notification_channel_id' => $channel->id,
    ]);
});

it('returns an error when a required field is missing', function(string $field) {
    $payload = storeUserPayload();
    unset($payload[$field]);

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'username' => ['field' => 'username'],
    'email' => ['field' => 'email'],
    'password' => ['field' => 'password'],
]);

it('returns an error for an invalid email', function() {
    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload(['email' => 'not-an-email']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('returns an error for a password that does not meet the requirements', function(string $password) {
    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload([
            'password' => $password,
            'passwordConfirmed' => $password,
        ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password' => __('validation.passwordTooWeak')]);
})->with([
    'too short' => ['password' => 'Ab1!def'],
    'no uppercase letter' => ['password' => 'str0ng!pass'],
    'no lowercase letter' => ['password' => 'STR0NG!PASS'],
    'no digit' => ['password' => 'Strong!Pass'],
    'no special character' => ['password' => 'Str0ngPass'],
]);

it('returns an error for an unconfirmed password', function() {
    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload(['passwordConfirmed' => 'Other1!Pass']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('password');
});

it('returns an error when the password confirmation is missing', function() {
    $payload = storeUserPayload();
    unset($payload['passwordConfirmed']);

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('passwordConfirmed');
});

it('returns an error for a username already taken', function() {
    User::factory()->create(['name' => 'msmith']);

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload())
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name' => __('validation.duplicatedName')]);
});

it('returns an error for an email already taken', function() {
    User::factory()->create(['email' => 'm.smith@system.local']);

    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload())
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email' => __('validation.duplicatedEmail')]);
});

it('does not create the user when the validation fails', function() {
    actingAsApiUser(PermissionType::USERS_CREATE)
        ->postJson('/api/users', storeUserPayload(['email' => 'not-an-email']))
        ->assertUnprocessable();

    $this->assertDatabaseCount('users', 1)
        ->assertDatabaseMissing('users', ['name' => 'msmith']);
});

it('has proper validation rules', function() {
    expect(new UserRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    actingAsApiUser()
        ->postJson('/api/users', storeUserPayload())
        ->assertForbidden();

    $this->assertDatabaseMissing('users', ['name' => 'msmith']);
});

it('returns an error on unauthorized request', function() {
    $this->postJson('/api/users', storeUserPayload())
        ->assertUnauthorized();

    $this->assertDatabaseMissing('users', ['name' => 'msmith']);
});
