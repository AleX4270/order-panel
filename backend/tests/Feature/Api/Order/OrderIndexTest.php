<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Http\Requests\Api\Order\OrderFilterRequest;
use App\Models\Address;
use App\Models\City;
use App\Models\Client;
use App\Models\Language;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Priority;
use App\ValueObjects\Coordinates;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Testing\Fluent\AssertableJson;

function createOrdersWithDates(): Collection {
    return Order::factory()
        ->count(5)
        ->state(new Sequence(
            ['id' => 1, 'created_at' => '2026-05-14 23:59:59', 'date_deadline' => '2026-06-10 10:00:00'],
            ['id' => 2, 'created_at' => '2026-05-15 00:00:00', 'date_deadline' => '2026-06-15 08:00:00'],
            ['id' => 3, 'created_at' => '2026-05-15 12:30:45', 'date_deadline' => '2026-06-15 12:30:45'],
            ['id' => 4, 'created_at' => '2026-05-15 23:59:59', 'date_deadline' => '2026-06-20 09:00:00'],
            ['id' => 5, 'created_at' => '2026-05-16 00:00:00', 'date_deadline' => '2026-06-15 23:59:59'],
        ))
        ->create();
}

function createOrdersForDictionaryFilters(): Collection {
    Priority::factory()
        ->count(3)
        ->state(new Sequence(['id' => 1], ['id' => 2], ['id' => 3]))
        ->create();

    OrderStatus::factory()
        ->count(3)
        ->state(new Sequence(['id' => 1], ['id' => 2], ['id' => 3]))
        ->create();

    return Order::factory()
        ->count(4)
        ->state(new Sequence(
            ['id' => 1, 'priority_id' => 1, 'status_id' => 1],
            ['id' => 2, 'priority_id' => 2, 'status_id' => 1],
            ['id' => 3, 'priority_id' => 1, 'status_id' => 2],
            ['id' => 4, 'priority_id' => 3, 'status_id' => 3],
        ))
        ->create();
}

function createOrdersForSorting(): Collection {
    Priority::factory()
        ->count(4)
        ->state(new Sequence(['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]))
        ->create();

    $orders = Order::factory()
        ->count(4)
        ->state(new Sequence(
            ['id' => 1, 'priority_id' => 2, 'created_at' => '2026-03-02 10:00:00', 'date_deadline' => '2026-04-03 10:00:00', 'address_id' => Address::factory()->state(['address' => 'Delta Street'])],
            ['id' => 2, 'priority_id' => 4, 'created_at' => '2026-03-04 10:00:00', 'date_deadline' => '2026-04-01 10:00:00', 'address_id' => Address::factory()->state(['address' => 'Alpha Street'])],
            ['id' => 3, 'priority_id' => 1, 'created_at' => '2026-03-01 10:00:00', 'date_deadline' => '2026-04-04 10:00:00', 'address_id' => Address::factory()->state(['address' => 'Charlie Street'])],
            ['id' => 4, 'priority_id' => 3, 'created_at' => '2026-03-03 10:00:00', 'date_deadline' => '2026-04-02 10:00:00', 'address_id' => Address::factory()->state(['address' => 'Bravo Street'])],
        ))
        ->create();

    $remarks = [1 => 'charlie', 2 => 'delta', 3 => 'alpha', 4 => 'bravo'];
    $orders->each(fn(Order $order) => $order->translations()->update(['remarks' => $remarks[$order->id]]));

    return $orders;
}

function createOrdersAtDistances(): Collection {
    return Order::factory()
        ->count(4)
        ->state(new Sequence(
            ['id' => 1, 'address_id' => Address::factory()->state(['coordinates' => new Coordinates(52.0, 21.0)])],
            ['id' => 2, 'address_id' => Address::factory()->state(['coordinates' => new Coordinates(52.5, 21.0)])],
            ['id' => 3, 'address_id' => Address::factory()->state(['coordinates' => new Coordinates(53.0, 21.0)])],
            ['id' => 4, 'address_id' => Address::factory()->state(['coordinates' => new Coordinates(54.0, 21.0)])],
        ))
        ->create();
}

it('returns all orders', function() {
    createHeadquarters();

    $orders = Order::factory()
        ->count(5)
        ->create()
        ->sortByDesc('id')
        ->values();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data.items')
        ->assertJsonPath('data.count', 5)
        ->assertJsonPath('data.items.*.id', $orders->pluck('id')->all())
        ->assertJsonPath('data.items.*.address', $orders->pluck('address.address')->all())
        ->assertJsonPath('data.items.*.postalCode', $orders->pluck('address.postal_code')->all())
        ->assertJsonPath('data.items.*.cityId', $orders->pluck('address.city_id')->all())
        ->assertJsonPath('data.items.*.cityName', $orders->pluck('address.city.name')->all())
        ->assertJsonPath('data.items.*.provinceId', $orders->pluck('address.city.province_id')->all())
        ->assertJsonPath('data.items.*.provinceName', $orders->pluck('address.city.province.name')->all())
        ->assertJsonPath('data.items.*.countryId', $orders->pluck('address.city.province.country_id')->all())
        ->assertJsonPath('data.items.*.priorityId', $orders->pluck('priority_id')->all())
        ->assertJsonPath('data.items.*.prioritySymbol', $orders->pluck('priority.symbol')->all())
        ->assertJsonPath('data.items.*.priorityName', $orders->pluck('priority.translations.0.name')->all())
        ->assertJsonPath('data.items.*.statusId', $orders->pluck('status_id')->all())
        ->assertJsonPath('data.items.*.statusSymbol', $orders->pluck('status.symbol')->all())
        ->assertJsonPath('data.items.*.statusName', $orders->pluck('status.translations.0.name')->all())
        ->assertJsonPath('data.items.*.phoneNumber', $orders->pluck('client.phone_number')->all())
        ->assertJsonPath('data.items.*.remarks', $orders->pluck('translations.0.remarks')->all())
        ->assertJsonPath('data.items.*.dateDeadline', $orders->pluck('date_deadline')->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->all())
        ->assertJsonPath('data.items.*.dateCompleted', [null, null, null, null, null]);
});

it('returns the dates formatted as plain dates', function() {
    createHeadquarters();

    Order::factory()->create([
        'created_at' => '2026-05-15 10:30:00',
        'date_deadline' => '2026-05-29 18:45:00',
        'date_completed' => '2026-05-20 08:15:00',
    ]);

    actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders')
        ->assertOk()
        ->assertJsonPath('data.items.0.dateCreated', '2026-05-15')
        ->assertJsonPath('data.items.0.dateDeadline', '2026-05-29')
        ->assertJsonPath('data.items.0.dateCompleted', '2026-05-20');
});

it('marks the orders past their deadline as overdue', function() {
    createHeadquarters();

    Order::factory()
        ->count(2)
        ->state(new Sequence(
            ['id' => 1, 'date_deadline' => now()->subDay()],
            ['id' => 2, 'date_deadline' => now()->addDay()],
        ))
        ->create();

    actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query(['sortColumn' => 'orderNumber', 'sortDir' => 'asc']))
        ->assertOk()
        ->assertJsonPath('data.items.*.isOverdue', [true, false]);
});

it('returns the priority and status names translated to the current locale', function(string $locale, string $expectedPriorityName, string $expectedStatusName, string $expectedRemarks) {
    createHeadquarters();

    $polish = Language::where('symbol', 'pl')->sole();
    $english = Language::where('symbol', 'en')->sole();

    $priority = Priority::factory()->create();
    $priority->translations()->delete();
    $priority->translations()->createMany([
        ['language_id' => $polish->id, 'name' => 'Wysoki'],
        ['language_id' => $english->id, 'name' => 'High'],
    ]);

    $status = OrderStatus::factory()->create();
    $status->translations()->delete();
    $status->translations()->createMany([
        ['language_id' => $polish->id, 'name' => 'W trakcie'],
        ['language_id' => $english->id, 'name' => 'In progress'],
    ]);

    $order = Order::factory()->create([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
    ]);
    $order->translations()->delete();
    $order->translations()->createMany([
        ['language_id' => $polish->id, 'remarks' => 'Uwagi'],
        ['language_id' => $english->id, 'remarks' => 'Remarks'],
    ]);

    App::setLocale($locale);

    actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders')
        ->assertOk()
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.items.0.priorityName', $expectedPriorityName)
        ->assertJsonPath('data.items.0.statusName', $expectedStatusName)
        ->assertJsonPath('data.items.0.remarks', $expectedRemarks);
})->with([
    'polish' => ['locale' => 'pl', 'expectedPriorityName' => 'Wysoki', 'expectedStatusName' => 'W trakcie', 'expectedRemarks' => 'Uwagi'],
    'english' => ['locale' => 'en', 'expectedPriorityName' => 'High', 'expectedStatusName' => 'In progress', 'expectedRemarks' => 'Remarks'],
]);

it('returns the coordinates of the order address', function() {
    createHeadquarters();

    Order::factory()
        ->for(Address::factory()->state([
            'coordinates' => new Coordinates(50.061389, 19.938333),
        ]))
        ->create();

    actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders')
        ->assertOk()
        ->assertJsonPath('data.items.0.coordinates.latitude', 50.061389)
        ->assertJsonPath('data.items.0.coordinates.longitude', 19.938333);
});

it('returns the distance from the headquarters in kilometers', function() {
    createHeadquarters();
    createOrdersAtDistances();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query(['sortColumn' => 'orderNumber', 'sortDir' => 'asc']));

    $response
        ->assertOk()
        ->assertJsonPath('data.items.0.distance', '0.00');

    $distances = array_map('floatval', $response->json('data.items.*.distance'));

    expect($distances[1])->toBeGreaterThan(55.0)->toBeLessThan(56.0)
        ->and($distances[2])->toBeGreaterThan(111.0)->toBeLessThan(112.0)
        ->and($distances[3])->toBeGreaterThan(222.0)->toBeLessThan(223.0);
});

it('returns orders from newest to oldest by default', function() {
    createHeadquarters();

    $orders = Order::factory()
        ->count(3)
        ->create();

    actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders')
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $orders->sortByDesc('id')->pluck('id')->values()->all());
});

it('does not return deleted orders', function() {
    createHeadquarters();

    $orders = Order::factory()
        ->count(3)
        ->create();

    $deletedOrders = Order::factory()
        ->inactive()
        ->count(2)
        ->create();

    actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders')
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(3, 'data.items')
        ->assertJsonPath('data.count', 3)
        ->assertJsonPath('data.items.*.id', $orders->sortByDesc('id')->pluck('id')->values()->all())
        ->assertJsonMissing(['id' => $deletedOrders->first()->id])
        ->assertJsonMissing(['id' => $deletedOrders->last()->id]);
});

it('returns orders matching provided term (allFields)', function(string $term, array $expectedIds) {
    createHeadquarters();

    $orders = Order::factory()
        ->count(4)
        ->state(new Sequence(
            [
                'id' => 1,
                'client_id' => Client::factory()->state([
                    'first_name' => 'John',
                    'last_name' => 'Reacher',
                    'email' => 'john@system.local',
                    'phone_number' => '+48 111 222 333',
                ]),
                'address_id' => Address::factory()->state([
                    'address' => 'Main Street 1',
                    'postal_code' => '00-001',
                    'city_id' => City::factory()->state(['name' => 'Warsaw']),
                ]),
            ],
            [
                'id' => 2,
                'client_id' => Client::factory()->state([
                    'first_name' => 'Anne',
                    'last_name' => 'Reacher',
                    'email' => 'anne@system.local',
                    'phone_number' => '+48 444 555 666',
                ]),
                'address_id' => Address::factory()->state([
                    'address' => 'Long Road 22',
                    'postal_code' => '30-002',
                    'city_id' => City::factory()->state(['name' => 'Krakow']),
                ]),
            ],
            [
                'id' => 13,
                'client_id' => Client::factory()->state([
                    'first_name' => 'Maria',
                    'last_name' => 'Smith',
                    'email' => 'm.smith@external.com',
                    'phone_number' => '+48 777 888 999',
                ]),
                'address_id' => Address::factory()->state([
                    'address' => 'Short Lane 13',
                    'postal_code' => '60-013',
                    'city_id' => City::factory()->state(['name' => 'Poznan']),
                ]),
            ],
            [
                'id' => 45,
                'client_id' => Client::factory()->state([
                    'first_name' => null,
                    'last_name' => null,
                    'email' => 'ghost@nowhere.test',
                    'phone_number' => '+48 222 333 444',
                ]),
                'address_id' => Address::factory()->state([
                    'address' => 'Ghost Alley 4',
                    'postal_code' => '99-999',
                    'city_id' => City::factory()->state(['name' => 'Gdansk']),
                ]),
            ],
        ))
        ->create();

    $remarks = [1 => 'Urgent delivery', 2 => 'Poznan office', 13 => 'Call before arrival', 45 => null];
    $orders->each(fn(Order $order) => $order->translations()->update(['remarks' => $remarks[$order->id]]));

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query(['allFields' => $term]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'address, exact' => ['term' => 'Main Street 1', 'expectedIds' => [1]],
    'address, infix in a different case' => ['term' => 'long road', 'expectedIds' => [2]],
    'postal code, exact' => ['term' => '30-002', 'expectedIds' => [2]],
    'postal code, prefix' => ['term' => '60-', 'expectedIds' => [13]],
    'city name, exact' => ['term' => 'Krakow', 'expectedIds' => [2]],
    'city name, different case' => ['term' => 'gdansk', 'expectedIds' => [45]],
    'client first name' => ['term' => 'Maria', 'expectedIds' => [13]],
    'client last name shared by two clients' => ['term' => 'Reacher', 'expectedIds' => [2, 1]],
    'client last name in a different case' => ['term' => 'reacher', 'expectedIds' => [2, 1]],
    'client first and last name joined are not matched' => ['term' => 'Maria Smith', 'expectedIds' => []],
    'client email, exact' => ['term' => 'john@system.local', 'expectedIds' => [1]],
    'client email, shared domain' => ['term' => '@system.local', 'expectedIds' => [2, 1]],
    'client phone number, infix' => ['term' => '444 555', 'expectedIds' => [2]],
    'remarks, infix' => ['term' => 'arrival', 'expectedIds' => [13]],
    'id, exact' => ['term' => '13', 'expectedIds' => [13]],
    'id, partial is not matched, only the phone number containing the digit is' => ['term' => '5', 'expectedIds' => [2]],
    'term matching a city of one order and remarks of another' => ['term' => 'Poznan', 'expectedIds' => [13, 2]],
    'term matching nothing' => ['term' => 'nonexistent', 'expectedIds' => []],
    'empty term returns every order' => ['term' => '', 'expectedIds' => [45, 13, 2, 1]],
    'term "0" is treated as empty and returns every order' => ['term' => '0', 'expectedIds' => [45, 13, 2, 1]],
]);

it('returns orders matching provided date, scoped to the whole day', function(string $filter, string $date, array $expectedIds) {
    createHeadquarters();
    createOrdersWithDates();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query([$filter => $date]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'dateCreation, whole day including both boundaries' => ['filter' => 'dateCreation', 'date' => '2026-05-15', 'expectedIds' => [4, 3, 2]],
    'dateCreation, last second of the previous day' => ['filter' => 'dateCreation', 'date' => '2026-05-14', 'expectedIds' => [1]],
    'dateCreation, first second of the next day' => ['filter' => 'dateCreation', 'date' => '2026-05-16', 'expectedIds' => [5]],
    'dateCreation, day without any order' => ['filter' => 'dateCreation', 'date' => '2026-05-17', 'expectedIds' => []],
    'dateCreation, time part of the term is ignored' => ['filter' => 'dateCreation', 'date' => '2026-05-15 18:45:00', 'expectedIds' => [4, 3, 2]],
    'dateDeadline, whole day including both boundaries' => ['filter' => 'dateDeadline', 'date' => '2026-06-15', 'expectedIds' => [5, 3, 2]],
    'dateDeadline, day matching a single order' => ['filter' => 'dateDeadline', 'date' => '2026-06-10', 'expectedIds' => [1]],
    'dateDeadline, day without any order' => ['filter' => 'dateDeadline', 'date' => '2026-06-21', 'expectedIds' => []],
    'dateDeadline, time part of the term is ignored' => ['filter' => 'dateDeadline', 'date' => '2026-06-15 04:20:00', 'expectedIds' => [5, 3, 2]],
]);

it('returns orders within the given distance from the headquarters', function(string $distance, array $expectedIds) {
    createHeadquarters();
    createOrdersAtDistances();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query(['distanceFromHeadquarters' => $distance]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'only the order at the headquarters' => ['distance' => '1', 'expectedIds' => [1]],
    'orders up to half a degree away' => ['distance' => '60', 'expectedIds' => [2, 1]],
    'orders up to a degree away' => ['distance' => '120', 'expectedIds' => [3, 2, 1]],
    'every order' => ['distance' => '300', 'expectedIds' => [4, 3, 2, 1]],
    'distance "0" is treated as no filter and returns every order' => ['distance' => '0', 'expectedIds' => [4, 3, 2, 1]],
]);

it('returns orders matching the given priorities', function(string $priorityIds, array $expectedIds) {
    createHeadquarters();
    createOrdersForDictionaryFilters();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query(['priorityIds' => $priorityIds]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'single priority' => ['priorityIds' => '1', 'expectedIds' => [3, 1]],
    'two priorities, comma separated' => ['priorityIds' => '2,3', 'expectedIds' => [4, 2]],
    'every priority' => ['priorityIds' => '1,2,3', 'expectedIds' => [4, 3, 2, 1]],
    'unknown priority' => ['priorityIds' => '99', 'expectedIds' => []],
]);

it('returns orders matching the given statuses', function(string $statusIds, array $expectedIds) {
    createHeadquarters();
    createOrdersForDictionaryFilters();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query(['statusIds' => $statusIds]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'single status' => ['statusIds' => '1', 'expectedIds' => [2, 1]],
    'two statuses, comma separated' => ['statusIds' => '2,3', 'expectedIds' => [4, 3]],
    'every status' => ['statusIds' => '1,2,3', 'expectedIds' => [4, 3, 2, 1]],
    'unknown status' => ['statusIds' => '99', 'expectedIds' => []],
]);

it('narrows results when several filters are provided', function() {
    createHeadquarters();
    createOrdersForDictionaryFilters();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query([
            'priorityIds' => '1',
            'statusIds' => '2',
        ]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(1, 'data.items')
        ->assertJsonPath('data.count', 1)
        ->assertJsonPath('data.items.*.id', [3]);
});

it('returns paginated data', function(int $page, int $size, int $itemsCount, int $expectedSize) {
    createHeadquarters();

    $orders = Order::factory()
        ->count($itemsCount)
        ->create();

    $expectedIds = $orders
        ->sortByDesc('id')
        ->slice(($page - 1) * $size, $size)
        ->pluck('id')
        ->all();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query(['page' => $page, 'pageSize' => $size]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount($expectedSize, 'data.items')
        ->assertJsonPath('data.count', $itemsCount)
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with('pagination');

it('returns data sorted by the requested column and direction', function(string $sortColumn, string $sortDir, array $expectedIds) {
    createHeadquarters();
    createOrdersForSorting();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query([
            'sortColumn' => $sortColumn,
            'sortDir' => $sortDir,
        ]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'orderNumber, ascending' => ['sortColumn' => 'orderNumber', 'sortDir' => 'asc', 'expectedIds' => [1, 2, 3, 4]],
    'orderNumber, descending' => ['sortColumn' => 'orderNumber', 'sortDir' => 'desc', 'expectedIds' => [4, 3, 2, 1]],
    'address, ascending' => ['sortColumn' => 'address', 'sortDir' => 'asc', 'expectedIds' => [2, 4, 3, 1]],
    'address, descending' => ['sortColumn' => 'address', 'sortDir' => 'desc', 'expectedIds' => [1, 3, 4, 2]],
    'priority, ascending' => ['sortColumn' => 'priority', 'sortDir' => 'asc', 'expectedIds' => [3, 1, 4, 2]],
    'priority, descending' => ['sortColumn' => 'priority', 'sortDir' => 'desc', 'expectedIds' => [2, 4, 1, 3]],
    'dateCreated, ascending' => ['sortColumn' => 'dateCreated', 'sortDir' => 'asc', 'expectedIds' => [3, 1, 4, 2]],
    'dateCreated, descending' => ['sortColumn' => 'dateCreated', 'sortDir' => 'desc', 'expectedIds' => [2, 4, 1, 3]],
    'dateDeadline, ascending' => ['sortColumn' => 'dateDeadline', 'sortDir' => 'asc', 'expectedIds' => [2, 4, 1, 3]],
    'dateDeadline, descending' => ['sortColumn' => 'dateDeadline', 'sortDir' => 'desc', 'expectedIds' => [3, 1, 4, 2]],
    'remarks, ascending' => ['sortColumn' => 'remarks', 'sortDir' => 'asc', 'expectedIds' => [3, 4, 1, 2]],
    'remarks, descending' => ['sortColumn' => 'remarks', 'sortDir' => 'desc', 'expectedIds' => [2, 1, 4, 3]],
]);

it('breaks ties on the address column with the postal code and the city', function(string $sortDir, array $expectedIds) {
    createHeadquarters();

    Order::factory()
        ->count(3)
        ->state(new Sequence(
            ['id' => 1, 'address_id' => Address::factory()->state(['address' => 'Main Street', 'postal_code' => '00-002', 'city_id' => City::factory()->state(['name' => 'Zabrze'])])],
            ['id' => 2, 'address_id' => Address::factory()->state(['address' => 'Main Street', 'postal_code' => '00-001', 'city_id' => City::factory()->state(['name' => 'Bytom'])])],
            ['id' => 3, 'address_id' => Address::factory()->state(['address' => 'Main Street', 'postal_code' => '00-001', 'city_id' => City::factory()->state(['name' => 'Aleksandrow'])])],
        ))
        ->create();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query([
            'sortColumn' => 'address',
            'sortDir' => $sortDir,
        ]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'ascending' => ['sortDir' => 'asc', 'expectedIds' => [3, 2, 1]],
    'descending' => ['sortDir' => 'desc', 'expectedIds' => [1, 2, 3]],
]);

it('falls back to descending id when the sort column is not supported', function(array $params) {
    createHeadquarters();
    createOrdersForSorting();

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders?'.http_build_query($params));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', [4, 3, 2, 1]);
})->with([
    'no sorting parameters at all' => ['params' => []],
    'unknown sort column' => ['params' => ['sortColumn' => 'status', 'sortDir' => 'asc']],
    'direction without a column is ignored' => ['params' => ['sortDir' => 'asc']],
]);

it('responds with valid api data structure', function() {
    createHeadquarters();

    Order::factory()
        ->count(3)
        ->create();

    Order::factory()->create(['date_completed' => now()]);

    $response = actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders');

    $response
        ->assertOk()
        ->assertJson(fn(AssertableJson $json) =>
            $json->has('timestamp')
                ->has('message')
                ->has('data', fn(AssertableJson $json) =>
                    $json->has('count')
                        ->has('items', 4, fn(AssertableJson $json) =>
                            $json->whereType('id', 'integer')
                                ->whereType('address', 'string')
                                ->whereType('postalCode', 'string|null')
                                ->whereType('cityId', 'integer')
                                ->whereType('cityName', 'string')
                                ->whereType('provinceId', 'integer')
                                ->whereType('provinceName', 'string')
                                ->whereType('countryId', 'integer')
                                ->whereType('priorityId', 'integer')
                                ->whereType('prioritySymbol', 'string')
                                ->whereType('priorityName', 'string')
                                ->whereType('statusId', 'integer')
                                ->whereType('statusSymbol', 'string')
                                ->whereType('statusName', 'string')
                                ->whereType('dateCreated', 'string')
                                ->whereType('dateDeadline', 'string')
                                ->whereType('dateCompleted', 'string|null')
                                ->whereType('phoneNumber', 'string')
                                ->whereType('remarks', 'string|null')
                                ->whereType('isOverdue', 'boolean')
                                ->whereType('distance', 'string')
                                ->has('coordinates', fn(AssertableJson $json) =>
                                    $json->whereType('latitude', 'double')
                                        ->whereType('longitude', 'double')
                                )
                        )
                )
        );
});

it('returns an error when the company is not configured', function() {
    Order::factory()->create();

    actingAsApiUser(PermissionType::ORDERS_VIEW)
        ->getJson('/api/orders')
        ->assertNotFound();
});

it('has proper validation rules', function() {
    expect(new OrderFilterRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    $response = actingAsApiUser()
        ->getJson('api/orders');

    $response
        ->assertForbidden();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/orders')
        ->assertUnauthorized();
});
