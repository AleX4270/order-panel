<?php

namespace Database\Seeders;

use App\Enums\RoleType;
use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $adminCredentials = [
            'name' => 'admin',
            'email' => config('admin.email'),
            'password' => config('admin.password'),
        ];

        if(empty($adminCredentials['email']) || empty($adminCredentials['password'])) {
            throw new Exception('The admin\'s email address and password are not specified. Please do that in the project\'s configuration.');
        }

        $existingAdmin = User::where(['email' => $adminCredentials['email']])->first();

        if(!empty($existingAdmin)) {
            return;
        }

        $admin = User::create([
            'name' => $adminCredentials['name'],
            'email' => $adminCredentials['email'],
            'password' => Hash::make($adminCredentials['password']),
            'is_internal' => 1,
        ]);

        $admin->assignRole(RoleType::ADMIN->value);
    }
}
