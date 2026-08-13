<?php

declare(strict_types=1);

use App\Models\User;

describe('user', function() {

});

describe('login', function() {

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