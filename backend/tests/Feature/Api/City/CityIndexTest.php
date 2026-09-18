<?php
declare(strict_types=1);

use App\Enums\SortDir;
use App\Http\Requests\Api\City\CityFilterRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns all cities', function() {
    $country = Country::factory()->create();
    $provinces = Province::factory()
        ->recycle($country)
        ->count(2)
        ->create();
        
    $cities = City::factory()
        ->recycle($provinces)
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/cities');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data.items')
        ->assertJsonPath('data.count', 5)
        ->assertJsonPath('data.items.*.id', $cities->pluck('id')->all())
        ->assertJsonPath('data.items.*.name', $cities->pluck('name')->all());
});

it('returns cities matching provided term', function(string $term) {
    $country = Country::factory()->create();
    $provinces = Province::factory()
        ->recycle($country)
        ->count(2)
        ->create();

    City::factory()
        ->count(2)
        ->recycle($provinces)
        ->state(new Sequence(
            ['name' => 'Poznań'],
            ['name' => 'Będzin'],
        ))
        ->create();

    $termCities = City::factory()
        ->count(3)
        ->recycle($provinces)
        ->state(new Sequence(
            ['name' => 'Sowaczewo'],
            ['name' => 'Kosowo'],
            ['name' => 'Sobótka'],
        ))
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/cities?term='.$term);

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(3, 'data.items')
        ->assertJsonPath('data.count', 3)
        ->assertJsonPath('data.items.*.name', $termCities->pluck('name')->all());
})->with(['So', 'so']);

it('returns cities for specified province', function() {
    $country = Country::factory()->create();
    $firstProvince = Province::factory()
        ->create([
            'country_id' => $country->id,
            'name' => 'Środkowy Śląsk',
        ]);

    $secondProvince = Province::factory()
        ->create([
            'country_id' => $country->id,
            'name' => 'Prawy Śląsk',
        ]);

    $resultCities = City::factory()
        ->count(2)
        ->recycle($firstProvince)
        ->state(new Sequence(
            ['name' => 'Poznań'],
            ['name' => 'Będzin'],
        ))
        ->create();

    City::factory()
        ->count(3)
        ->recycle($secondProvince)
        ->state(new Sequence(
            ['name' => 'Sowaczewo'],
            ['name' => 'Kosowo'],
            ['name' => 'Sobótka'],
        ))
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/cities?provinceId='.$firstProvince->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.count', 2)
        ->assertJsonPath('data.items.*.name', $resultCities->pluck('name')->all());
});

it('returns paginated data', function(int $page, int $size, int $itemsCount, int $expectedSize) {
    $country = Country::factory()->create();
    $provinces = Province::factory()
        ->recycle($country)
        ->count(2)
        ->create();
        
    $cities = City::factory()
        ->recycle($provinces)
        ->count($itemsCount)
        ->create();

    $expectedIds = $cities->slice(($page-1) * $size, $size)->pluck('id');

    $response = actingAsApiUser()
        ->getJson("/api/cities?page={$page}&pageSize={$size}");

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount($expectedSize, 'data.items')
        ->assertJsonPath('data.count', $itemsCount)
        ->assertJsonPath('data.items.*.id', $expectedIds->toArray());
})->with('pagination');

it('returns data sorted by name', function(SortDir $sortDir, array $expectedSequence) {
    $country = Country::factory()->create();
    $provinces = Province::factory()
        ->recycle($country)
        ->count(2)
        ->create();
        
    City::factory()
        ->recycle($provinces)
        ->count(4)
        ->sequence(
            ['name' => 'Łódź'],
            ['name' => 'Żywiec'],
            ['name' => 'Będzin'],
            ['name' => 'Sopot'],
        )
        ->create();

    $response = actingAsApiUser()
        ->getJson("/api/cities?sortColumn=name&sortDir={$sortDir->value}");

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.name', $expectedSequence);
})->with([
    'ascending' => ['sortDir' => SortDir::ASC, 'expectedSequence' => ['Będzin', 'Łódź', 'Sopot', 'Żywiec']],
    'descending' => ['sortDir' => SortDir::DESC, 'expectedSequence' => ['Żywiec', 'Sopot', 'Łódź', 'Będzin']],
]);

it('returns data sorted by default with ascending id', function() {
    $country = Country::factory()->create();
    $provinces = Province::factory()
        ->recycle($country)
        ->count(2)
        ->create();
        
    $cities = City::factory()
        ->recycle($provinces)
        ->count(4)
        ->create();

    $expectedSequence = $cities->sortBy('id')->pluck('id')->toArray();

    $response = actingAsApiUser()
        ->getJson("/api/cities");

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $expectedSequence);
});

it('responds with valid api data structure', function() {
    $country = Country::factory()->create();
    $provinces = Province::factory()
        ->recycle($country)
        ->count(2)
        ->create();
        
    City::factory()
        ->recycle($provinces)
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/cities');

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
                                ->whereType('name', 'string')
                        )
                    )
                )
            );
});

it('has proper validation rules', function() {
    expect(new CityFilterRequest()->rules())->toMatchSnapshot();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/cities')
        ->assertUnauthorized();
});
