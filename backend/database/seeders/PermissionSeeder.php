<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\PermissionType;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder {
    public function run(): void {
        foreach(PermissionType::cases() as $permission) {
            Permission::findOrCreate($permission->value);
        }
    }
}
