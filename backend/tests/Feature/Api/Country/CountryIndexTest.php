<?php
declare(strict_types=1);

use App\Http\Requests\Api\Country\CountryFilterRequest;
use App\Models\Country;
use App\Models\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns all countries', function() {
    $countries = Country::factory()
        ->count(5)
        ->create();

    $response = sessionRequest()
        ->getJson('/api/countries');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data.items')
        ->assertJsonPath('data.count', 5)
        ->assertJsonPath('data.items.*.id', $countries->pluck('id')->all())
        ->assertJsonPath('data.items.*.symbol', $countries->pluck('symbol')->all());
});

it('returns country names translated to the current locale', function(string $locale, string $expectedName) {
    $country = Country::factory()->create(['symbol' => 'PL']);
    $country->translations()->delete();
    $country->translations()->createMany([
        [
            'language_id' => Language::where('symbol', 'pl')->sole()->id,
            'name' => 'Polska',
        ],
        [
            'language_id' => Language::where('symbol', 'en')->sole()->id,
            'name' => 'Poland',
        ],
    ]);

    App::setLocale($locale);

    $response = sessionRequest()
        ->getJson('/api/countries');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.symbol', 'PL')
        ->assertJsonPath('data.items.0.name', $expectedName);
})->with([
    'polish' => ['locale' => 'pl', 'expectedName' => 'Polska'],
    'english' => ['locale' => 'en', 'expectedName' => 'Poland'],
]);

it('does not return countries missing a translation for the current locale', function() {
    $countries = Country::factory()
        ->count(2)
        ->create();

    $untranslatedCountry = Country::factory()->create();
    $untranslatedCountry->translations()->delete();

    $response = sessionRequest()
        ->getJson('/api/countries');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.count', 2)
        ->assertJsonPath('data.items.*.id', $countries->pluck('id')->all())
        ->assertJsonMissing(['id' => $untranslatedCountry->id]);
});

it('returns paginated data', function(int $page, int $size, int $itemsCount, int $expectedSize) {
    $countries = Country::factory()
        ->count($itemsCount)
        ->create();

    $expectedIds = $countries->slice(($page-1) * $size, $size)->pluck('id');

    $response = sessionRequest()
        ->getJson("/api/countries?page={$page}&pageSize={$size}");

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount($expectedSize, 'data.items')
        ->assertJsonPath('data.count', $itemsCount)
        ->assertJsonPath('data.items.*.id', $expectedIds->toArray());
})->with('pagination');

it('returns data sorted by default with ascending id', function() {
    $countries = Country::factory()
        ->count(4)
        ->create();

    $expectedSequence = $countries->sortBy('id')->pluck('id')->toArray();

    $response = sessionRequest()
        ->getJson('/api/countries');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $expectedSequence);
});

it('responds with valid api data structure', function() {
    Country::factory()
        ->count(5)
        ->create();

    $response = sessionRequest()
        ->getJson('/api/countries');

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
    expect(new CountryFilterRequest()->rules())->toMatchSnapshot();
});

it('is available without authentication', function() {
    Country::factory()->create();

    $this->getJson('/api/countries')
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(1, 'data.items');
});
