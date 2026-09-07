<?php
declare(strict_types=1);

use App\Enums\PermissionType;
use App\Http\Requests\Api\User\UserFilterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function() {
    User::query()->delete();
});

function createUsersWithDates(): Collection {
    return User::factory()
        ->count(5)
        ->state(new Sequence(
            ['id' => 1, 'created_at' => '2026-05-14 23:59:59', 'updated_at' => '2026-06-10 10:00:00'],
            ['id' => 2, 'created_at' => '2026-05-15 00:00:00', 'updated_at' => '2026-06-15 08:00:00'],
            ['id' => 3, 'created_at' => '2026-05-15 12:30:45', 'updated_at' => '2026-06-15 12:30:45'],
            ['id' => 4, 'created_at' => '2026-05-15 23:59:59', 'updated_at' => '2026-06-20 09:00:00'],
            ['id' => 5, 'created_at' => '2026-05-16 00:00:00', 'updated_at' => '2026-06-15 23:59:59'],
        ))
        ->create();
}

function createUsersForSorting(): Collection {
    return User::factory()
        ->count(4)
        ->state(new Sequence(
            ['id' => 1, 'name' => 'delta',   'email' => 'c@sort.test', 'created_at' => '2026-03-02 10:00:00', 'updated_at' => '2026-07-03 10:00:00'],
            ['id' => 2, 'name' => 'alpha',   'email' => 'd@sort.test', 'created_at' => '2026-03-04 10:00:00', 'updated_at' => '2026-07-01 10:00:00'],
            ['id' => 3, 'name' => 'charlie', 'email' => 'a@sort.test', 'created_at' => '2026-03-01 10:00:00', 'updated_at' => '2026-07-04 10:00:00'],
            ['id' => 4, 'name' => 'bravo',   'email' => 'b@sort.test', 'created_at' => '2026-03-03 10:00:00', 'updated_at' => '2026-07-02 10:00:00'],
        ))
        ->create();
}

it('returns all users', function() {
    $users = User::factory()
        ->count(5)
        ->create();

    $response = actingAsUser($users->first(), PermissionType::USERS_VIEW)
        ->getJson('/api/users');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(5, 'data.items')
        ->assertJsonPath('data.count', 5)
        ->assertJsonPath('data.items.*.id', $users->pluck('id')->all())
        ->assertJsonPath('data.items.*.symbol', $users->pluck('symbol')->all());
});

it('returns users matching provided term (allFields)', function(string $term, array $expectedIds) {
    $users = User::factory()
        ->count(4)
        ->state(new Sequence(
            [
                'id' => 1,
                'name' => 'johnd',
                'email' => 'johnd@system.local',
                'first_name' => 'John',
                'last_name' => 'Reacher',
            ],
            [
                'id' => 2,
                'name' => 'polaten20',
                'email' => 'polaten@system.local',
                'first_name' => 'Anne',
                'last_name' => 'Reacher',
            ],
            [
                'id' => 13,
                'name' => 'msmith',
                'email' => 'm.smith@external.com',
                'first_name' => 'Maria',
                'last_name' => 'Smith',
            ],
            [
                'id' => 45,
                'name' => 'ghost',
                'email' => 'ghost@nowhere.test',
                'first_name' => null,
                'last_name' => null,
            ],
        ))
        ->create();

    $response = actingAsUser($users->first(), PermissionType::USERS_VIEW)
        ->getJson('/api/users?'.http_build_query(['allFields' => $term]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'username, exact' => ['term' => 'johnd', 'expectedIds' => [1]],
    'username, prefix' => ['term' => 'pola', 'expectedIds' => [2]],
    'username, infix' => ['term' => 'aten', 'expectedIds' => [2]],
    'username, different case' => ['term' => 'POLA', 'expectedIds' => [2]],
    'email, exact' => ['term' => 'johnd@system.local', 'expectedIds' => [1]],
    'email, shared domain' => ['term' => '@system.local', 'expectedIds' => [1, 2]],
    'email, different case' => ['term' => 'M.SMITH', 'expectedIds' => [13]],
    'id, exact' => ['term' => '13', 'expectedIds' => [13]],
    'id, partial matches every id containing the digit' => ['term' => '1', 'expectedIds' => [1, 13]],
    'id, digit also present in a username' => ['term' => '2', 'expectedIds' => [2]],
    'full name, first name only' => ['term' => 'Maria', 'expectedIds' => [13]],
    'full name, last name shared by two users' => ['term' => 'Reacher', 'expectedIds' => [1, 2]],
    'full name, last name in a different case' => ['term' => 'reacher', 'expectedIds' => [1, 2]],
    'full name, first and last name joined' => ['term' => 'Maria Smith', 'expectedIds' => [13]],
    'full name, term spanning the separating space' => ['term' => 'n Rea', 'expectedIds' => [1]],
    'full name, parts taken from two different users' => ['term' => 'John Smith', 'expectedIds' => []],
    'term matching a username of one user and an email of another' => ['term' => 'ghost', 'expectedIds' => [45]],
    'term matching nothing' => ['term' => 'nonexistent', 'expectedIds' => []],
    'term matching nothing, user without a first and last name' => ['term' => 'Ghost Ghost', 'expectedIds' => []],
    'empty term returns every user' => ['term' => '', 'expectedIds' => [1, 2, 13, 45]],
    'term "0" is treated as empty and returns every user' => ['term' => '0', 'expectedIds' => [1, 2, 13, 45]],
]);

it('returns users matching provided date, scoped to the whole day', function(string $filter, string $date, array $expectedIds) {
    $users = createUsersWithDates();

    $response = actingAsUser($users->first(), PermissionType::USERS_VIEW)
        ->getJson('/api/users?'.http_build_query([$filter => $date]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'dateCreated, whole day including both boundaries' => ['filter' => 'dateCreated', 'date' => '2026-05-15', 'expectedIds' => [2, 3, 4]],
    'dateCreated, last second of the previous day' => ['filter' => 'dateCreated', 'date' => '2026-05-14', 'expectedIds' => [1]],
    'dateCreated, first second of the next day' => ['filter' => 'dateCreated', 'date' => '2026-05-16', 'expectedIds' => [5]],
    'dateCreated, day without any user' => ['filter' => 'dateCreated', 'date' => '2026-05-17', 'expectedIds' => []],
    'dateCreated, time part of the term is ignored' => ['filter' => 'dateCreated', 'date' => '2026-05-15 18:45:00', 'expectedIds' => [2, 3, 4]],
    'dateUpdated, whole day including both boundaries' => ['filter' => 'dateUpdated', 'date' => '2026-06-15', 'expectedIds' => [2, 3, 5]],
    'dateUpdated, day matching a single user' => ['filter' => 'dateUpdated', 'date' => '2026-06-10', 'expectedIds' => [1]],
    'dateUpdated, day without any user' => ['filter' => 'dateUpdated', 'date' => '2026-06-21', 'expectedIds' => []],
    'dateUpdated, time part of the term is ignored' => ['filter' => 'dateUpdated', 'date' => '2026-06-15 04:20:00', 'expectedIds' => [2, 3, 5]],
]);

it('narrows results when both date filters are provided', function(string $dateCreated, string $dateUpdated, array $expectedIds) {
    $users = createUsersWithDates();

    $response = actingAsUser($users->first(), PermissionType::USERS_VIEW)
        ->getJson('/api/users?'.http_build_query([
            'dateCreated' => $dateCreated,
            'dateUpdated' => $dateUpdated,
        ]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(count($expectedIds), 'data.items')
        ->assertJsonPath('data.count', count($expectedIds))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'both days overlapping on a subset of users' => [
        'dateCreated' => '2026-05-15',
        'dateUpdated' => '2026-06-15',
        'expectedIds' => [2, 3],
    ],
    'days matching disjoint sets of users' => [
        'dateCreated' => '2026-05-15',
        'dateUpdated' => '2026-06-10',
        'expectedIds' => [],
    ],
]);

it('does not return inactive users', function() {
    $activeUsers = User::factory()
        ->count(3)
        ->create();

    $inactiveUsers = User::factory()
        ->inactive()
        ->count(2)
        ->create();

    $response = actingAsUser($activeUsers->first(), PermissionType::USERS_VIEW)
        ->getJson('/api/users');

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount(3, 'data.items')
        ->assertJsonPath('data.count', 3)
        ->assertJsonPath('data.items.*.id', $activeUsers->pluck('id')->all())
        ->assertJsonMissing(['id' => $inactiveUsers->first()->id])
        ->assertJsonMissing(['id' => $inactiveUsers->last()->id]);
});

it('returns paginated data', function(int $page, int $size, int $itemsCount, int $expectedSize) {
    $users = User::factory()
        ->count($itemsCount)
        ->create();

    $expectedIds = $users
        ->sortBy('id')
        ->slice(($page - 1) * $size, $size)
        ->pluck('id')
        ->all();

    $response = actingAsUser($users->first(), PermissionType::USERS_VIEW)
        ->getJson('/api/users?'.http_build_query(['page' => $page, 'pageSize' => $size]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonCount($expectedSize, 'data.items')
        ->assertJsonPath('data.count', $itemsCount)
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with('pagination');

it('returns data sorted by the requested column and direction', function(string $sortColumn, string $sortDir, array $expectedIds) {
    $users = createUsersForSorting();

    $response = actingAsUser($users->first(), PermissionType::USERS_VIEW)
        ->getJson('/api/users?'.http_build_query([
            'sortColumn' => $sortColumn,
            'sortDir' => $sortDir,
        ]));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', $expectedIds);
})->with([
    'userNumber, ascending' => ['sortColumn' => 'userNumber', 'sortDir' => 'asc', 'expectedIds' => [1, 2, 3, 4]],
    'userNumber, descending' => ['sortColumn' => 'userNumber', 'sortDir' => 'desc', 'expectedIds' => [4, 3, 2, 1]],
    'name, ascending' => ['sortColumn' => 'name', 'sortDir' => 'asc', 'expectedIds' => [2, 4, 3, 1]],
    'name, descending' => ['sortColumn' => 'name', 'sortDir' => 'desc', 'expectedIds' => [1, 3, 4, 2]],
    'email, ascending' => ['sortColumn' => 'email', 'sortDir' => 'asc', 'expectedIds' => [3, 4, 1, 2]],
    'email, descending' => ['sortColumn' => 'email', 'sortDir' => 'desc', 'expectedIds' => [2, 1, 4, 3]],
    'dateCreated, ascending' => ['sortColumn' => 'dateCreated', 'sortDir' => 'asc', 'expectedIds' => [3, 1, 4, 2]],
    'dateCreated, descending' => ['sortColumn' => 'dateCreated', 'sortDir' => 'desc', 'expectedIds' => [2, 4, 1, 3]],
    'dateUpdated, ascending' => ['sortColumn' => 'dateUpdated', 'sortDir' => 'asc', 'expectedIds' => [2, 4, 1, 3]],
    'dateUpdated, descending' => ['sortColumn' => 'dateUpdated', 'sortDir' => 'desc', 'expectedIds' => [3, 1, 4, 2]],
]);

it('falls back to ascending id when the sort column is not supported', function(array $params) {
    $users = createUsersForSorting();

    $response = actingAsUser($users->first(), PermissionType::USERS_VIEW)
        ->getJson('/api/users?'.http_build_query($params));

    $response
        ->assertOk()
        ->assertJsonPath('message', __('response.success'))
        ->assertJsonPath('data.items.*.id', [1, 2, 3, 4]);
})->with([
    'no sorting parameters at all' => ['params' => []],
    'unknown sort column' => ['params' => ['sortColumn' => 'firstName', 'sortDir' => 'desc']],
    'direction without a column is ignored' => ['params' => ['sortDir' => 'desc']],
]);

it('breaks ties on the name column with the first and last name', function(string $sortDir, array $expectedIds) {
    $users = User::factory()
        ->count(3)
        ->state(new Sequence(
            ['id' => 1, 'name' => 'kowal', 'first_name' => 'Zofia', 'last_name' => 'Nowak'],
            ['id' => 2, 'name' => 'kowal', 'first_name' => 'Adam', 'last_name' => 'Zielinski'],
            ['id' => 3, 'name' => 'kowal', 'first_name' => 'Adam', 'last_name' => 'Abacki'],
        ))
        ->create();

    $response = actingAsUser($users->first(), PermissionType::USERS_VIEW)
        ->getJson('/api/users?'.http_build_query([
            'sortColumn' => 'name',
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

it('responds with valid api data structure', function() {
    $role = Role::factory()->create();

    $users = User::factory()
        ->count(4)
        ->state(fn() => [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
        ])
        ->create()
        ->each(fn(User $user) => $user->assignRole($role));

    $response = actingAsUser($users->first(), PermissionType::USERS_VIEW)
        ->getJson('/api/users');

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
                                    ->whereType('firstName', 'string|null')
                                    ->whereType('lastName', 'string|null')
                                    ->whereType('email', 'string')
                                    ->whereType('dateCreated', 'string')
                                    ->whereType('dateUpdated', 'string')
                                    ->whereType('isInternal', 'boolean')
                                    ->has('roles', fn(AssertableJson $json) =>
                                        $json->each(fn(AssertableJson $json) =>
                                            $json->whereType('id', 'integer')
                                                ->whereType('symbol', 'string')
                                                ->whereType('name', 'string|null')
                                        )
                                    )
                            )
                        )
                    )
            );
});

it('has proper validation rules', function() {
    expect(new UserFilterRequest()->rules())->toMatchSnapshot();
});

it('returns an error for user without required permissions', function() {
    $response = actingAsApiUser()
        ->getJson('api/users');

    $response
        ->assertForbidden();
});

it('returns an error on unauthorized request', function() {
    $this->getJson('/api/users')
        ->assertUnauthorized();
});
