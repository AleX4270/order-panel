<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Http\Requests\Api\User\UserDeleteRequest;
use App\Models\User;

beforeEach(function() {
    User::query()->delete();
});

it('deactivates the requested user', function() {
    $actingUser = User::factory()->create();
    $deletedUser = User::factory()->create();

    $response = actingAsUser($actingUser, PermissionType::USERS_DELETE)
        ->deleteJson('/api/users/'.$deletedUser->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.deleted'));

    $this->assertDatabaseHas('users', [
        'id' => $deletedUser->id,
        'is_active' => false,
    ]);
});

it('keeps the deleted user in the database', function() {
    $actingUser = User::factory()->create();
    $deletedUser = User::factory()->create(['email' => 'kept@system.local']);

    actingAsUser($actingUser, PermissionType::USERS_DELETE)
        ->deleteJson('/api/users/'.$deletedUser->id)
        ->assertOk();

    $this->assertDatabaseHas('users', [
        'id' => $deletedUser->id,
        'email' => 'kept@system.local',
    ]);
});

it('does not touch the other users', function() {
    $actingUser = User::factory()->create();
    $deletedUser = User::factory()->create();
    $untouchedUser = User::factory()->create();

    actingAsUser($actingUser, PermissionType::USERS_DELETE)
        ->deleteJson('/api/users/'.$deletedUser->id)
        ->assertOk();

    $this->assertDatabaseHas('users', [
        'id' => $untouchedUser->id,
        'is_active' => true,
    ]);
});

it('deactivates an already inactive user without an error', function() {
    $actingUser = User::factory()->create();
    $inactiveUser = User::factory()->inactive()->create();

    actingAsUser($actingUser, PermissionType::USERS_DELETE)
        ->deleteJson('/api/users/'.$inactiveUser->id)
        ->assertOk()
        ->assertJsonPath('message', __('response.deleted'));

    $this->assertDatabaseHas('users', [
        'id' => $inactiveUser->id,
        'is_active' => false,
    ]);
});

it('lets a user deactivate their own account', function() {
    $actingUser = User::factory()->create();

    actingAsUser($actingUser, PermissionType::USERS_DELETE)
        ->deleteJson('/api/users/'.$actingUser->id)
        ->assertOk();

    $this->assertDatabaseHas('users', [
        'id' => $actingUser->id,
        'is_active' => false,
    ]);
});

it('returns an error when the user does not exist', function() {
    $actingUser = User::factory()->create();

    actingAsUser($actingUser, PermissionType::USERS_DELETE)
        ->deleteJson('/api/users/'.($actingUser->id + 1000))
        ->assertNotFound();
});

it('returns an error for an id that is not a positive integer', function(string $id) {
    $actingUser = User::factory()->create();

    actingAsUser($actingUser, PermissionType::USERS_DELETE)
        ->deleteJson('/api/users/'.$id)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('id');
})->with([
    'not a number' => ['id' => 'abc'],
    'zero' => ['id' => '0'],
    'negative' => ['id' => '-3'],
    'decimal' => ['id' => '1.5'],
]);

it('returns an error when deleting an internal user', function() {
    $actingUser = User::factory()->create();
    $internalUser = User::factory()->create(['is_internal' => true]);

    actingAsUser($actingUser, PermissionType::USERS_DELETE)
        ->deleteJson('/api/users/'.$internalUser->id)
        ->assertForbidden()
        ->assertJsonPath('message', __('response.internalUserCannotBeDeleted'));

    $this->assertDatabaseHas('users', [
        'id' => $internalUser->id,
        'is_active' => true,
    ]);
});

it('has proper validation rules', function() {
    expect(new UserDeleteRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    $deletedUser = User::factory()->create();

    actingAsApiUser()
        ->deleteJson('/api/users/'.$deletedUser->id)
        ->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $deletedUser->id,
        'is_active' => true,
    ]);
});

it('returns an error on unauthorized request', function() {
    $deletedUser = User::factory()->create();

    $this->deleteJson('/api/users/'.$deletedUser->id)
        ->assertUnauthorized();

    $this->assertDatabaseHas('users', [
        'id' => $deletedUser->id,
        'is_active' => true,
    ]);
});
