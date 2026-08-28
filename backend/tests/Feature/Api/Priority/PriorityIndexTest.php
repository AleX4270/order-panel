<?php
declare(strict_types=1);

use App\Http\Requests\Api\Priority\PriorityFilterRequest;
use App\Models\Language;
use App\Models\Priority;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns all priorities', function() {
    $priorities = Priority::factory()
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/priorities');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data.items')
        ->assertJsonPath('data.count', 5)
        ->assertJsonPath('data.items.*.id', $priorities->pluck('id')->all())
        ->assertJsonPath('data.items.*.symbol', $priorities->pluck('symbol')->all());
});

it('returns priorities matching provided term', function(string $term) {
    $priorities = Priority::factory()
        ->count(5)
        ->create();

    $names = ['Normalny', 'Wysoki', 'Pilny', 'Bardzo pilny', 'Superpilny'];
    $priorities->each(fn(Priority $priority, int $index) => 
        $priority->translations()->update(['name' => $names[$index]])
    );

    $termPriorities = $priorities->slice(2);

    $response = actingAsApiUser()
        ->getJson('/api/priorities?term='.$term);

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(3, 'data.items')
        ->assertJsonPath('data.count', 3)
        ->assertJsonPath('data.items.*.id', $termPriorities->pluck('id')->all())
        ->assertJsonPath('data.items.*.name', ['Pilny', 'Bardzo pilny', 'Superpilny']);
})->with(['Pi', 'pi']);

it('does not return inactive priorities', function() {
    $priorities = Priority::factory()
        ->count(2)
        ->create();

    $inactivePriority = Priority::factory()
        ->inactive()
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/priorities');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.count', 2)
        ->assertJsonPath('data.items.*.id', $priorities->pluck('id')->all())
        ->assertJsonMissing(['id' => $inactivePriority->id]);
});

it('returns priority names translated to the current locale', function(string $locale, string $expectedName) {
    $priority = Priority::factory()->create(['symbol' => 'high']);
    $priority->translations()->delete();
    $priority->translations()->createMany([
        [
            'language_id' => Language::where('symbol', 'pl')->sole()->id,
            'name' => 'Wysoki',
        ],
        [
            'language_id' => Language::where('symbol', 'en')->sole()->id,
            'name' => 'High',
        ],
    ]);

    App::setLocale($locale);

    $response = actingAsApiUser()
        ->getJson('/api/priorities');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.symbol', 'high')
        ->assertJsonPath('data.items.0.name', $expectedName);
})->with([
    'polish' => ['locale' => 'pl', 'expectedName' => 'Wysoki'],
    'english' => ['locale' => 'en', 'expectedName' => 'High'],
]);

it('does not return priorities missing a translation for the current locale', function() {
    $priorities = Priority::factory()
        ->count(2)
        ->create();

    $untranslatedPriority = Priority::factory()->create();
    $untranslatedPriority->translations()->delete();

    $response = actingAsApiUser()
        ->getJson('/api/priorities');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.count', 2)
        ->assertJsonPath('data.items.*.id', $priorities->pluck('id')->all())
        ->assertJsonMissing(['id' => $untranslatedPriority->id]);
});

it('returns paginated data', function(int $page, int $size, int $itemsCount, int $expectedSize) {
    $priorities = Priority::factory()
        ->count($itemsCount)
        ->create();

    $expectedIds = $priorities->slice(($page-1) * $size, $size)->pluck('id');

    $response = actingAsApiUser()
        ->getJson("/api/priorities?page={$page}&pageSize={$size}");

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount($expectedSize, 'data.items')
        ->assertJsonPath('data.count', $itemsCount)
        ->assertJsonPath('data.items.*.id', $expectedIds->toArray());
})->with('pagination');

it('returns data sorted by default with ascending id', function() {
    $priorities = Priority::factory()
        ->count(4)
        ->create();

    $expectedSequence = $priorities->sortBy('id')->pluck('id')->toArray();

    $response = actingAsApiUser()
        ->getJson('/api/priorities');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $expectedSequence);
});

it('responds with valid api data structure', function() {
    Priority::factory()
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/priorities');

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
                                ->whereType('isActive', 'boolean')
                                ->whereType('name', 'string')
                        )
                    )
                )
            );
});

it('has proper validation rules', function() {
    expect(new PriorityFilterRequest()->rules())->toMatchSnapshot();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/priorities')
        ->assertUnauthorized();
});
