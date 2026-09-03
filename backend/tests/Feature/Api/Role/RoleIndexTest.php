<?php
declare(strict_types=1);
use App\Http\Requests\Api\Role\RoleFilterRequest;
use App\Models\Language;
use App\Models\Role;
use App\Models\RoleTranslation;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function() {
    RoleTranslation::query()->delete();
    Role::query()->delete();
});

it('returns all roles', function() {
    $roles = Role::factory()
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/roles');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data.items')
        ->assertJsonPath('data.count', 5)
        ->assertJsonPath('data.items.*.id', $roles->pluck('id')->all())
        ->assertJsonPath('data.items.*.symbol', $roles->pluck('name')->all())
        ->assertJsonPath('data.items.*.name', $roles->pluck('translations.0.name')->all());
});

it('returns paginated data', function(int $page, int $size, int $itemsCount, int $expectedSize) {
    $roles = Role::factory()
        ->count($itemsCount)
        ->create();

    $expectedIds = $roles->slice(($page-1) * $size, $size)->pluck('id');

    $response = actingAsApiUser()
        ->getJson("/api/roles?page={$page}&pageSize={$size}");

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount($expectedSize, 'data.items')
        ->assertJsonPath('data.count', $itemsCount)
        ->assertJsonPath('data.items.*.id', $expectedIds->toArray());
})->with('pagination');

it('returns data sorted by default with ascending id', function() {
    $roles = Role::factory()
        ->count(4)
        ->create();

    $expectedSequence = $roles->sortBy('id')->pluck('id')->toArray();

    $response = actingAsApiUser()
        ->getJson('/api/roles');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $expectedSequence);
});

it('returns role names translated to the current locale', function(string $locale, string $expectedName) {
    $role = Role::factory()->create(['name' => 'manager']);
    $role->translations()->delete();
    $role->translations()->createMany([
        [
            'language_id' => Language::where('symbol', 'pl')->sole()->id,
            'name' => 'Kierownik',
        ],
        [
            'language_id' => Language::where('symbol', 'en')->sole()->id,
            'name' => 'Manager',
        ],
    ]);

    App::setLocale($locale);

    $response = actingAsApiUser()
        ->getJson('/api/roles');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.symbol', 'manager')
        ->assertJsonPath('data.items.0.name', $expectedName);
})->with([
    'polish' => ['locale' => 'pl', 'expectedName' => 'Kierownik'],
    'english' => ['locale' => 'en', 'expectedName' => 'Manager'],
]);

it('does not return roles missing a translation for the current locale', function() {
    $roles = Role::factory()
        ->count(2)
        ->create();

    $untranslatedRole = Role::factory()->create();
    $untranslatedRole->translations()->delete();

    $response = actingAsApiUser()
        ->getJson('/api/roles');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.count', 2)
        ->assertJsonPath('data.items.*.id', $roles->pluck('id')->all())
        ->assertJsonMissing(['id' => $untranslatedRole->id]);
});

it('responds with valid api data structure', function() {
    Role::factory()
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/roles');

    $response
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) => 
            $json->has('timestamp')
            ->has('message')
            ->has('data', fn(AssertableJson $json) =>
                $json->has('count')
                    ->has('items', fn(AssertableJson $json) => 
                        $json->each(fn(AssertableJson $json) => 
                            $json->whereType('id', 'integer')
                                ->whereType('symbol', 'string')
                                ->whereType('name', 'string')
                        )
                    )
                )
            );
});

it('has proper validation rules', function() {
    expect(new RoleFilterRequest()->rules())->toMatchSnapshot();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/roles')
        ->assertUnauthorized();
});
