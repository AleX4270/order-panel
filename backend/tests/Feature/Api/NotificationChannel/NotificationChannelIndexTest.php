<?php
declare(strict_types=1);

use App\Http\Requests\Api\NotificationChannel\NotificationChannelFilterRequest;
use App\Models\Language;
use Database\Factories\NotificationChannelFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns all notification channels', function() {
    $notificationChannels = NotificationChannelFactory::new()
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/notification-channels');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('data.*.id', $notificationChannels->pluck('id')->all())
        ->assertJsonPath('data.*.symbol', $notificationChannels->pluck('symbol')->all())
        ->assertJsonPath('data.*.name', $notificationChannels->pluck('translations.0.name')->all());
});

it('returns notification channel names translated to the current locale', function(string $locale, string $expectedName) {
    $notificationChannel = NotificationChannelFactory::new()->create(['symbol' => 'mail']);
    $notificationChannel->translations()->delete();
    $notificationChannel->translations()->createMany([
        [
            'language_id' => Language::where('symbol', 'pl')->sole()->id,
            'name' => 'Powiadomienia e-mail',
        ],
        [
            'language_id' => Language::where('symbol', 'en')->sole()->id,
            'name' => 'Email notifications',
        ],
    ]);

    App::setLocale($locale);

    $response = actingAsApiUser()
        ->getJson('/api/notification-channels');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.symbol', 'mail')
        ->assertJsonPath('data.0.name', $expectedName);
})->with([
    'polish' => ['locale' => 'pl', 'expectedName' => 'Powiadomienia e-mail'],
    'english' => ['locale' => 'en', 'expectedName' => 'Email notifications'],
]);

it('does not return notification channels missing a translation for the current locale', function() {
    $notificationChannels = NotificationChannelFactory::new()
        ->count(2)
        ->create();

    $untranslatedChannel = NotificationChannelFactory::new()->create();
    $untranslatedChannel->translations()->delete();

    $response = actingAsApiUser()
        ->getJson('/api/notification-channels');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.*.id', $notificationChannels->pluck('id')->all())
        ->assertJsonMissing(['id' => $untranslatedChannel->id]);
});

it('returns data sorted by default with ascending id', function() {
    $notificationChannels = NotificationChannelFactory::new()
        ->count(4)
        ->create();

    $expectedSequence = $notificationChannels->sortBy('id')->pluck('id')->toArray();

    $response = actingAsApiUser()
        ->getJson('/api/notification-channels');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.*.id', $expectedSequence);
});

it('responds with valid api data structure', function() {
    NotificationChannelFactory::new()
        ->count(5)
        ->create();

    $response = actingAsApiUser()
        ->getJson('/api/notification-channels');

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
    expect(new NotificationChannelFilterRequest()->rules())->toMatchSnapshot();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/notification-channels')
        ->assertUnauthorized();
});
