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

function updateUserPayload(User $user, array $overrides = []): array {
    return array_merge([
        'id' => $user->id,
        'firstName' => 'Maria',
        'lastName' => 'Smith',
        'username' => 'msmith',
        'email' => 'm.smith@system.local',
        'notificationSettings' => [],
    ], $overrides);
}

it('updates the requested user', function() {
    $user = User::factory()->create([
        'name' => 'mkowalska',
        'first_name' => 'Marta',
        'last_name' => 'Kowalska',
        'email' => 'm.kowalska@system.local',
    ]);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user))
        ->assertNoContent();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'msmith',
        'first_name' => 'Maria',
        'last_name' => 'Smith',
        'email' => 'm.smith@system.local',
    ])
        ->assertDatabaseMissing('users', ['name' => 'mkowalska']);
});

it('does not create a new user', function() {
    $user = User::factory()->create();

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user))
        ->assertNoContent();

    $this->assertDatabaseCount('users', 2);
});

it('does not touch the other users', function() {
    $user = User::factory()->create();
    $untouchedUser = User::factory()->create([
        'name' => 'untouched',
        'email' => 'untouched@system.local',
    ]);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user))
        ->assertNoContent();

    $this->assertDatabaseHas('users', [
        'id' => $untouchedUser->id,
        'name' => 'untouched',
        'email' => 'untouched@system.local',
    ]);
});

it('updates the password when it is given', function() {
    $user = User::factory()->create(['password' => 'Old1!Password']);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user, [
            'password' => 'New1!Password',
            'passwordConfirmed' => 'New1!Password',
        ]))
        ->assertNoContent();

    expect(Hash::check('New1!Password', $user->refresh()->password))->toBeTrue();
});

it('keeps the current password when it is not given', function() {
    $user = User::factory()->create(['password' => 'Old1!Password']);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user))
        ->assertNoContent();

    expect(Hash::check('Old1!Password', $user->refresh()->password))->toBeTrue();
});

it('replaces the roles assigned to the user', function() {
    $currentRole = Role::factory()->create();
    $newRole = Role::factory()->create();

    $user = User::factory()->create();
    $user->assignRole($currentRole);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user, [
            'roles' => [$newRole->name],
        ]))
        ->assertNoContent();

    $this->assertDatabaseHas('model_has_roles', [
        'role_id' => $newRole->id,
        'model_id' => $user->id,
    ])
        ->assertDatabaseMissing('model_has_roles', [
            'role_id' => $currentRole->id,
            'model_id' => $user->id,
        ]);
});

it('removes the roles when none is given', function() {
    $role = Role::factory()->create();

    $user = User::factory()->create();
    $user->assignRole($role);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user))
        ->assertNoContent();

    $this->assertDatabaseMissing('model_has_roles', [
        'model_id' => $user->id,
    ]);
});

it('keeps the roles of an internal user', function() {
    $role = Role::factory()->create();
    $newRole = Role::factory()->create();

    $user = User::factory()->create(['is_internal' => true]);
    $user->assignRole($role);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user, [
            'roles' => [$newRole->name],
        ]))
        ->assertNoContent();

    $this->assertDatabaseHas('model_has_roles', [
        'role_id' => $role->id,
        'model_id' => $user->id,
    ])
        ->assertDatabaseMissing('model_has_roles', [
            'role_id' => $newRole->id,
            'model_id' => $user->id,
        ]);
});

it('replaces the notification settings', function() {
    $events = NotificationEvent::factory()->count(2)->create();
    $channels = NotificationChannel::factory()->count(2)->create();

    $user = User::factory()->create();
    $user->notificationSettings()->create([
        'notification_event_id' => $events->first()->id,
        'notification_channel_id' => $channels->first()->id,
    ]);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user, [
            'notificationSettings' => [
                [
                    'eventId' => $events->last()->id,
                    'channelIds' => $channels->pluck('id')->all(),
                ],
            ],
        ]))
        ->assertNoContent();

    $this->assertDatabaseCount('user_notification_settings', 2)
        ->assertDatabaseHas('user_notification_settings', [
            'user_id' => $user->id,
            'notification_event_id' => $events->last()->id,
            'notification_channel_id' => $channels->first()->id,
        ])
        ->assertDatabaseHas('user_notification_settings', [
            'user_id' => $user->id,
            'notification_event_id' => $events->last()->id,
            'notification_channel_id' => $channels->last()->id,
        ])
        ->assertDatabaseMissing('user_notification_settings', [
            'user_id' => $user->id,
            'notification_event_id' => $events->first()->id,
        ]);
});

it('removes the notification settings when an empty list is given', function() {
    $event = NotificationEvent::factory()->create();
    $channel = NotificationChannel::factory()->create();

    $user = User::factory()->create();
    $user->notificationSettings()->create([
        'notification_event_id' => $event->id,
        'notification_channel_id' => $channel->id,
    ]);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user))
        ->assertNoContent();

    $this->assertDatabaseMissing('user_notification_settings', [
        'user_id' => $user->id,
    ]);
});

it('does not touch the notification settings of the other users', function() {
    $event = NotificationEvent::factory()->create();
    $channel = NotificationChannel::factory()->create();

    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherUser->notificationSettings()->create([
        'notification_event_id' => $event->id,
        'notification_channel_id' => $channel->id,
    ]);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user))
        ->assertNoContent();

    $this->assertDatabaseHas('user_notification_settings', [
        'user_id' => $otherUser->id,
        'notification_event_id' => $event->id,
        'notification_channel_id' => $channel->id,
    ]);
});

it('keeps the username and the email of the updated user', function() {
    $user = User::factory()->create([
        'name' => 'msmith',
        'email' => 'm.smith@system.local',
    ]);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user, [
            'firstName' => 'Marianna',
        ]))
        ->assertNoContent();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'msmith',
        'email' => 'm.smith@system.local',
        'first_name' => 'Marianna',
    ]);
});

it('returns an error when the user does not exist', function() {
    $user = User::factory()->create();

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user, [
            'id' => $user->id + 1000,
        ]))
        ->assertNotFound();

    $this->assertDatabaseMissing('users', ['name' => 'msmith']);
});

it('returns an error when a required field is missing', function(string $field) {
    $user = User::factory()->create();

    $payload = updateUserPayload($user);
    unset($payload[$field]);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'username' => ['field' => 'username'],
    'email' => ['field' => 'email'],
]);

it('returns an error for an invalid email', function() {
    $user = User::factory()->create();

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user, ['email' => 'not-an-email']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('returns an error for a password that does not meet the requirements', function() {
    $user = User::factory()->create();

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user, [
            'password' => 'weakpassword',
            'passwordConfirmed' => 'weakpassword',
        ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password' => __('validation.passwordTooWeak')]);
});

it('returns an error for an unconfirmed password', function() {
    $user = User::factory()->create();

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user, [
            'password' => 'New1!Password',
            'passwordConfirmed' => 'Other1!Password',
        ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('password');
});

it('returns an error for a username taken by another user', function() {
    $user = User::factory()->create();
    User::factory()->create(['name' => 'msmith']);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name' => __('validation.duplicatedName')]);
});

it('returns an error for an email taken by another user', function() {
    $user = User::factory()->create();
    User::factory()->create(['email' => 'm.smith@system.local']);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email' => __('validation.duplicatedEmail')]);
});

it('does not update the user when the validation fails', function() {
    $user = User::factory()->create(['name' => 'mkowalska']);

    actingAsApiUser(PermissionType::USERS_UPDATE)
        ->putJson('/api/users', updateUserPayload($user, ['email' => 'not-an-email']))
        ->assertUnprocessable();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'mkowalska',
    ]);
});

it('has proper validation rules', function() {
    expect(new UserRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    $user = User::factory()->create(['name' => 'mkowalska']);

    actingAsApiUser()
        ->putJson('/api/users', updateUserPayload($user))
        ->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'mkowalska',
    ]);
});

it('returns an error on unauthorized request', function() {
    $user = User::factory()->create(['name' => 'mkowalska']);

    $this->putJson('/api/users', updateUserPayload($user))
        ->assertUnauthorized();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'mkowalska',
    ]);
});
