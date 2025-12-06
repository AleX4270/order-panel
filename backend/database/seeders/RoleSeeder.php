<?php
namespace Database\Seeders;

use App\Enums\LanguageType;
use Illuminate\Database\Seeder;
use App\Enums\PermissionType;
use App\Enums\RoleType;
use App\Models\Language;
use App\Models\RoleTranslation;
use Exception;
use App\Models\Role;

class RoleSeeder extends Seeder {
    public function run(): void {
        foreach($this->getRoles() as $data) {
            $role = Role::findOrCreate($data['name']);

            foreach($data['translations'] as $language => $translation) {
                $languageId = Language::where('symbol', $language)->first()?->id;

                if(empty($languageId)) {
                    throw new Exception('Language ID was not found');
                }

                RoleTranslation::updateOrCreate(
                    ['role_id' => $role->id, 'language_id' => $languageId],
                    ['name' => $translation],
                );
            }

            $role->givePermissionTo($data['permissions']);
        }
    }

    private function getRoles(): array {
        return [
            [
                'name' => RoleType::ADMIN->value,
                'translations' => [
                    LanguageType::PL->value => 'Administrator',
                    LanguageType::EN->value => 'Administrator',
                ],
                'permissions' => PermissionType::all(),
            ],
            [
                'name' => RoleType::MANAGER->value,
                'translations' => [
                    LanguageType::PL->value => 'Kierownik',
                    LanguageType::EN->value => 'Manager',
                ],
                'permissions' => [
                    PermissionType::ORDERS_VIEW->value,
                    PermissionType::ORDERS_SHOW->value,
                    PermissionType::ORDERS_CREATE->value,
                    PermissionType::ORDERS_UPDATE->value,
                    PermissionType::ORDERS_DELETE->value,
                ],
            ],
            [
                'name' => RoleType::WORKER->value,
                'translations' => [
                    LanguageType::PL->value => 'Pracownik',
                    LanguageType::EN->value => 'Worker',
                ],
                'permissions' => [
                    PermissionType::ORDERS_VIEW->value,
                    PermissionType::ORDERS_SHOW->value,
                ],
            ],
        ];
    }
}
