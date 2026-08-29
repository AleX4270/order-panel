<?php

declare(strict_types=1);

use App\Enums\PermissionType;
use App\Models\User;
use Tests\TestCase;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

function actingAsApiUser(PermissionType ...$permissions): TestCase {
    $user = User::factory()->create();

    if(!empty($permissions)) {
        $user->syncPermissions(array_map(fn($permission) => $permission->value, $permissions));
    }

    return test()
        ->actingAs($user, 'web')
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