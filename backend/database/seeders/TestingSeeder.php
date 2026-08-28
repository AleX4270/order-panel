<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestingSeeder extends Seeder {
    public function run(): void {
        $this->call([
            LanguageSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
            NotificationChannelSeeder::class,
            NotificationEventSeeder::class,
        ]);
    }
}
