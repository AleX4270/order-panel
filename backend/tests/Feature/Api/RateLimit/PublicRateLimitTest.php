<?php
declare(strict_types=1);

use App\Models\User;
use Illuminate\Testing\TestResponse;

const PUBLIC_RATE_LIMIT = 20;

function exhaustPublicRateLimit(array $headers = [], array $serverVariables = []): TestResponse {
    $response = null;

    foreach(range(1, PUBLIC_RATE_LIMIT) as $attempt) {
        $response = test()
            ->withServerVariables($serverVariables)
            ->withHeaders($headers)
            ->getJson('/api/countries');

        $response->assertOk();
    }

    return $response;
}

it('allows a guest to make the limited number of requests per hour', function() {
    $response = exhaustPublicRateLimit();

    $response
        ->assertOk()
        ->assertHeader('X-RateLimit-Limit', PUBLIC_RATE_LIMIT)
        ->assertHeader('X-RateLimit-Remaining', 0);
});

it('counts down the remaining attempts in the headers', function() {
    $this->getJson('/api/countries')
        ->assertOk()
        ->assertHeader('X-RateLimit-Limit', PUBLIC_RATE_LIMIT)
        ->assertHeader('X-RateLimit-Remaining', PUBLIC_RATE_LIMIT - 1);

    $this->getJson('/api/countries')
        ->assertOk()
        ->assertHeader('X-RateLimit-Remaining', PUBLIC_RATE_LIMIT - 2);
});

it('rejects a guest exceeding the limit', function() {
    exhaustPublicRateLimit();

    $this->getJson('/api/countries')
        ->assertTooManyRequests()
        ->assertJsonPath('message', __('response.tooManyAttempts'))
        ->assertHeader('X-RateLimit-Limit', PUBLIC_RATE_LIMIT)
        ->assertHeader('X-RateLimit-Remaining', 0)
        ->assertHeader('Retry-After');
});

it('shares the limit between all public endpoints', function() {
    exhaustPublicRateLimit();

    $this->postJson('/api/order-requests')
        ->assertTooManyRequests()
        ->assertJsonPath('message', __('response.tooManyAttempts'));

    $this->getJson('/api/provinces')
        ->assertTooManyRequests();

    $this->postJson('/api/login')
        ->assertTooManyRequests();
});

it('allows the guest again once an hour has passed', function() {
    exhaustPublicRateLimit();

    $this->getJson('/api/countries')
        ->assertTooManyRequests();

    $this->travel(61)->minutes();

    $this->getJson('/api/countries')
        ->assertOk()
        ->assertHeader('X-RateLimit-Remaining', PUBLIC_RATE_LIMIT - 1);
});

it('keeps a separate limit for every ip address and user agent pair', function(array $headers, array $serverVariables) {
    exhaustPublicRateLimit();

    $this->getJson('/api/countries')
        ->assertTooManyRequests();

    $this->withServerVariables($serverVariables)
        ->withHeaders($headers)
        ->getJson('/api/countries')
        ->assertOk()
        ->assertHeader('X-RateLimit-Remaining', PUBLIC_RATE_LIMIT - 1);
})->with([
    'different ip address' => ['headers' => [], 'serverVariables' => ['REMOTE_ADDR' => '10.0.0.2']],
    'different user agent' => ['headers' => ['User-Agent' => 'OtherAgent/1.0'], 'serverVariables' => []],
]);

it('does not limit authenticated users', function() {
    $user = User::factory()->create();

    foreach(range(1, PUBLIC_RATE_LIMIT + 1) as $attempt) {
        actingAsUser($user)
            ->getJson('/api/countries')
            ->assertOk()
            ->assertHeaderMissing('X-RateLimit-Limit');
    }
});
