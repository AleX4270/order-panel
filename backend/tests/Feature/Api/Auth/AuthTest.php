<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Auth;

describe('user', function() {
    it('returns an error on unauthorized request', function() {
        $this->getJson('/api/user')
            ->assertUnauthorized();
    });

    it('returns authenticated user\'s data', function() {
        $result = actingAsApiUser()
            ->getJson('/api/user');

        $user = Auth::user();

        $result->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', $user->name)
            ->assertJsonPath('message', __('response.success'));
    });
});

describe('login', function() {
    it('logs the user in successfully', function() {
        $credentials = [
            'email' => 'testuser@system.local',
            'password' => 'testpassword',
        ];

        $user = User::factory()->create($credentials);

        sessionRequest()
            ->postJson('/api/login', $credentials)
            ->assertOk()
            ->assertJsonPath('message', __('response.success'))
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.name', $user->name)
            ->assertJsonStructure(['data' => ['permissions']]);

        $this->assertAuthenticatedAs($user, 'web');
    });

    it('throws an error for invalid credentials', function() {
        $credentials = [
            'email' => 'testuser@system.local',
            'password' => 'testpassword',
        ];

        User::factory()->create($credentials);

        sessionRequest()
            ->postJson('/api/login', [...$credentials, 'password' => 'testpassword1'])
            ->assertUnauthorized()
            ->assertJsonPath('message', __('auth.failed'));

        $this->assertGuest('web');
    });

    it('throws an error for inactive user', function() {
        $credentials = [
            'email' => 'testuser@system.local',
            'password' => 'testpassword',
        ];

        User::factory()->create([...$credentials, 'is_active' => 0]);

        sessionRequest()
            ->postJson('/api/login', $credentials)
            ->assertUnauthorized()
            ->assertJsonPath('message', __('auth.failed'));

        $this->assertGuest('web');
    });

    it('throws an error for missing password', function() {
        $credentials = [
            'email' => 'testuser@system.local',
        ];

        sessionRequest()
            ->postJson('/api/login', $credentials)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password' => __('auth.passwordRequired')]);

        $this->assertGuest('web');
    });

    it('throws an error for uppercase email', function() {
        $credentials = [
            'email' => 'TestUser@system.local',
            'password' => 'testpassword',
        ];

        sessionRequest()
            ->postJson('/api/login', $credentials)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => __('auth.emailLowercase')]);

        $this->assertGuest('web');
    });
});

describe('logout', function() {
    it('logs the user out successfully', function() {
        actingAsApiUser()
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('message', __('response.success'));

        $this->assertGuest('web');
    });

    it('returns an error on unauthorized request', function() {
        $this->postJson('/api/logout')
            ->assertUnauthorized();
    });
});