<?php

declare(strict_types=1);

use App\Models\User;
use Tests\TestCase;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

function actingAsApiUser(): TestCase {
    return test()
        ->actingAs(User::factory()->create(), 'web')
        ->withHeaders([
            'Origin' => 'http://localhost'
        ]);
}

function sessionRequest(): TestCase {
    return test()
        ->withHeaders([
            'Origin' => 'http://localhost'
        ]);
}