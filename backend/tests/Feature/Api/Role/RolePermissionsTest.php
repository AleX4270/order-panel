<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

it('assigns proper permissions to roles', function() {
    $rolePermissions = Role::with('permissions')
        ->orderBy('name')
        ->get()
        ->mapWithKeys(fn(Role $role) => [
            $role->name => $role->permissions->pluck('name')->sort()->values()->all(),
        ])
        ->all();

    expect($rolePermissions)->toMatchSnapshot();
});

it('creates every defined permission', function() {
    $permissions = Permission::query()
        ->pluck('name')
        ->sort()
        ->values()
        ->all();

    $expected = collect(PermissionType::all())->sort()->values()->all();

    expect($permissions)->toBe($expected);
});
