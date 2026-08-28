<?php
declare(strict_types=1);

use App\Http\Requests\Api\Status\StatusFilterRequest;
use App\Models\Language;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns all statuses', function() {
    $statuses = OrderStatus::factory()
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/statuses');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data.items')
        ->assertJsonPath('data.count', 5)
        ->assertJsonPath('data.items.*.id', $statuses->pluck('id')->all())
        ->assertJsonPath('data.items.*.symbol', $statuses->pluck('symbol')->all());
});

it('returns statuses matching provided term', function(string $term) {
    $statuses = OrderStatus::factory()
        ->count(5)
        ->create();

    $names = ['W trakcie', 'Nowy', 'Zakończony', 'Wznowiony', 'Wykonany'];
    $statuses->each(fn(OrderStatus $status, int $index) => 
        $status->translations()->update(['name' => $names[$index]])
    );

    $termStatuses = $statuses->slice(2);

    $response = actingAsApiUser()
        ->getJson('/api/statuses?term='.$term);

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(3, 'data.items')
        ->assertJsonPath('data.count', 3)
        ->assertJsonPath('data.items.*.id', $termStatuses->pluck('id')->all())
        ->assertJsonPath('data.items.*.name', ['Zakończony', 'Wznowiony', 'Wykonany']);
})->with(['ON', 'on']);

it('does not return inactive statuses', function() {
    $statuses = OrderStatus::factory()
        ->count(2)
        ->create();

    $inactiveStatus = OrderStatus::factory()
        ->inactive()
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/statuses');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.count', 2)
        ->assertJsonPath('data.items.*.id', $statuses->pluck('id')->all())
        ->assertJsonMissing(['id' => $inactiveStatus->id]);
});

it('returns status names translated to the current locale', function(string $locale, string $expectedName) {
    $status = OrderStatus::factory()->create(['symbol' => 'completed']);
    $status->translations()->delete();
    $status->translations()->createMany([
        [
            'language_id' => Language::where('symbol', 'pl')->sole()->id,
            'name' => 'Zakończony',
        ],
        [
            'language_id' => Language::where('symbol', 'en')->sole()->id,
            'name' => 'Completed',
        ],
    ]);

    App::setLocale($locale);

    $response = actingAsApiUser()
        ->getJson('/api/statuses');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.symbol', 'completed')
        ->assertJsonPath('data.items.0.name', $expectedName);
})->with([
    'polish' => ['locale' => 'pl', 'expectedName' => 'Zakończony'],
    'english' => ['locale' => 'en', 'expectedName' => 'Completed'],
]);

it('does not return statuses missing a translation for the current locale', function() {
    $statuses = OrderStatus::factory()
        ->count(2)
        ->create();

    $untranslatedStatus = OrderStatus::factory()->create();
    $untranslatedStatus->translations()->delete();

    $response = actingAsApiUser()
        ->getJson('/api/statuses');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(2, 'data.items')
        ->assertJsonPath('data.count', 2)
        ->assertJsonPath('data.items.*.id', $statuses->pluck('id')->all())
        ->assertJsonMissing(['id' => $untranslatedStatus->id]);
});

it('returns paginated data', function(int $page, int $size, int $itemsCount, int $expectedSize) {
    $statuses = OrderStatus::factory()
        ->count($itemsCount)
        ->create();

    $expectedIds = $statuses->slice(($page-1) * $size, $size)->pluck('id');

    $response = actingAsApiUser()
        ->getJson("/api/statuses?page={$page}&pageSize={$size}");

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount($expectedSize, 'data.items')
        ->assertJsonPath('data.count', $itemsCount)
        ->assertJsonPath('data.items.*.id', $expectedIds->toArray());
})->with('pagination');

it('returns data sorted by default with ascending id', function() {
    $statuses = OrderStatus::factory()
        ->count(4)
        ->create();

    $expectedSequence = $statuses->sortBy('id')->pluck('id')->toArray();

    $response = actingAsApiUser()
        ->getJson('/api/statuses');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $expectedSequence);
});

it('responds with valid api data structure', function() {
    OrderStatus::factory()
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/statuses');

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
    expect(new StatusFilterRequest()->rules())->toMatchSnapshot();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/statuses')
        ->assertUnauthorized();
});
