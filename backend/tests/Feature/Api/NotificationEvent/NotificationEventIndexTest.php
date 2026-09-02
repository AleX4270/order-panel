<?php
declare(strict_types=1);

use App\Http\Requests\Api\NotificationEvent\NotificationEventFilterRequest;
use App\Models\Language;
use Database\Factories\NotificationEventFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns all notification events', function() {
    $notificationEvents = NotificationEventFactory::new()
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/notification-events');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('data.*.id', $notificationEvents->pluck('id')->all())
        ->assertJsonPath('data.*.symbol', $notificationEvents->pluck('symbol')->all())
        ->assertJsonPath('data.*.name', $notificationEvents->pluck('translations.0.name')->all());
});

it('returns notification event names translated to the current locale', function(string $locale, string $expectedName) {
    $notificationEvent = NotificationEventFactory::new()->create(['symbol' => 'order_completed']);
    $notificationEvent->translations()->delete();
    $notificationEvent->translations()->createMany([
        [
            'language_id' => Language::where('symbol', 'pl')->sole()->id,
            'name' => 'Oznaczenie zlecenia jako ukończone',
        ],
        [
            'language_id' => Language::where('symbol', 'en')->sole()->id,
            'name' => 'Order marked as completed',
        ],
    ]);

    App::setLocale($locale);

    $response = actingAsApiUser()
        ->getJson('/api/notification-events');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.symbol', 'order_completed')
        ->assertJsonPath('data.0.name', $expectedName);
})->with([
    'polish' => ['locale' => 'pl', 'expectedName' => 'Oznaczenie zlecenia jako ukończone'],
    'english' => ['locale' => 'en', 'expectedName' => 'Order marked as completed'],
]);

it('does not return notification events missing a translation for the current locale', function() {
    $notificationEvents = NotificationEventFactory::new()
        ->count(2)
        ->create();

    $untranslatedEvent = NotificationEventFactory::new()->create();
    $untranslatedEvent->translations()->delete();

    $response = actingAsApiUser()
        ->getJson('/api/notification-events');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.*.id', $notificationEvents->pluck('id')->all())
        ->assertJsonMissing(['id' => $untranslatedEvent->id]);
});

it('returns data sorted by default with ascending id', function() {
    $notificationEvents = NotificationEventFactory::new()
        ->count(4)
        ->create();

    $expectedSequence = $notificationEvents->sortBy('id')->pluck('id')->toArray();

    $response = actingAsApiUser()
        ->getJson('/api/notification-events');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.*.id', $expectedSequence);
});

it('responds with valid api data structure', function() {
    NotificationEventFactory::new()
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/notification-events');

    $response
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) =>
            $json->has('timestamp')
                ->has('message')
                ->has('data', 5, fn(AssertableJson $json) =>
                    $json->whereType('id', 'integer')
                        ->whereType('symbol', 'string')
                        ->whereType('name', 'string')
                )
        );
});

it('has proper validation rules', function() {
    expect(new NotificationEventFilterRequest()->rules())->toMatchSnapshot();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/notification-events')
        ->assertUnauthorized();
});
