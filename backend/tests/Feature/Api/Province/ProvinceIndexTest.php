<?php
declare(strict_types=1);

use App\Http\Requests\Api\Province\ProvinceFilterRequest;
use App\Models\Country;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns all provinces', function() {
    $country = Country::factory()->create();
    $provinces = Province::factory()
        ->recycle($country)
        ->count(5)
        ->create();

    $response = sessionRequest()
        ->getJson('/api/provinces');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data.items')
        ->assertJsonPath('data.count', 5)
        ->assertJsonPath('data.items.*.id', $provinces->pluck('id')->all())
        ->assertJsonPath('data.items.*.name', $provinces->pluck('name')->all());
});

it('returns provinces for specified country', function() {
    $firstCountry = Country::factory()->create(['symbol' => 'PL']);
    $secondCountry = Country::factory()->create(['symbol' => 'DE']);

    $resultProvinces = Province::factory()
        ->count(2)
        ->recycle($firstCountry)
        ->state(new Sequence(
            ['name' => 'Śląskie'],
            ['name' => 'Wielkopolskie'],
        ))
        ->create();

    Province::factory()
        ->count(3)
        ->recycle($secondCountry)
        ->state(new Sequence(
            ['name' => 'Bawaria'],
            ['name' => 'Saksonia'],
            ['name' => 'Hesja'],
        ))
        ->create();

    $response = sessionRequest()
        ->getJson('/api/provinces?countryId='.$firstCountry->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.count', 2)
        ->assertJsonPath('data.items.*.id', $resultProvinces->pluck('id')->all())
        ->assertJsonPath('data.items.*.name', $resultProvinces->pluck('name')->all());
});

it('returns paginated data', function(int $page, int $size, int $itemsCount, int $expectedSize) {
    $country = Country::factory()->create();
    $provinces = Province::factory()
        ->recycle($country)
        ->count($itemsCount)
        ->create();

    $expectedIds = $provinces->slice(($page-1) * $size, $size)->pluck('id');

    $response = sessionRequest()
        ->getJson("/api/provinces?page={$page}&pageSize={$size}");

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount($expectedSize, 'data.items')
        ->assertJsonPath('data.count', $itemsCount)
        ->assertJsonPath('data.items.*.id', $expectedIds->toArray());
})->with('pagination');

it('returns data sorted by default with ascending id', function() {
    $country = Country::factory()->create();
    $provinces = Province::factory()
        ->recycle($country)
        ->count(4)
        ->create();

    $expectedSequence = $provinces->sortBy('id')->pluck('id')->toArray();

    $response = sessionRequest()
        ->getJson('/api/provinces');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $expectedSequence);
});

it('responds with valid api data structure', function() {
    $country = Country::factory()->create();
    Province::factory()
        ->recycle($country)
        ->count(5)
        ->create();

    $response = sessionRequest()
        ->getJson('/api/provinces');

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
    expect(new ProvinceFilterRequest()->rules())->toMatchSnapshot();
});

it('is available without authentication', function() {
    $country = Country::factory()->create();
    Province::factory()
        ->recycle($country)
        ->create();

    $this->getJson('/api/provinces')
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(1, 'data.items');
});
