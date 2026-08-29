<?php
declare(strict_types=1);
use App\Enums\PermissionType;
use App\Http\Requests\Api\Company\CompanyRequest;
use App\Models\Company;
use Illuminate\Testing\Fluent\AssertableJson;

describe('show', function() {
    it('returns current company', function() {
        $company = Company::factory()
            ->create();
            
        $response = actingAsApiUser()
            ->getJson('api/company');

        $response
            ->assertOk()
            ->assertJsonPath('message', __('response.success'))
            ->assertJsonPath('data.id', $company->id)
            ->assertJsonPath('data.name', $company->name)
            ->assertJsonPath('data.address', $company->address->address)
            ->assertJsonPath('data.postalCode', $company->address->postal_code)
            ->assertJsonPath('data.cityId', $company->address->city_id)
            ->assertJsonPath('data.provinceId', $company->address->city->province_id)
            ->assertJsonPath('data.countryId', $company->address->city->province->country_id)
            ->assertJsonPath('data.coordinates.latitude', $company->address->latitude)
            ->assertJsonPath('data.coordinates.longitude', $company->address->longitude);
    });

    it('returns first company', function() {
        $firstCompany = Company::factory()
            ->create();

        Company::factory()
            ->create();
            
        $response = actingAsApiUser()
            ->getJson('api/company');

        $response
            ->assertOk()
            ->assertJsonPath('message', __('response.success'))
            ->assertJsonPath('data.id', $firstCompany->id)
            ->assertJsonPath('data.name', $firstCompany->name)
            ->assertJsonPath('data.address', $firstCompany->address->address)
            ->assertJsonPath('data.postalCode', $firstCompany->address->postal_code)
            ->assertJsonPath('data.cityId', $firstCompany->address->city_id)
            ->assertJsonPath('data.provinceId', $firstCompany->address->city->province_id)
            ->assertJsonPath('data.countryId', $firstCompany->address->city->province->country_id)
            ->assertJsonPath('data.coordinates.latitude', $firstCompany->address->latitude)
            ->assertJsonPath('data.coordinates.longitude', $firstCompany->address->longitude);
    });

    it('returns an error when the company is not configured', function() {
        $response = actingAsApiUser()
            ->getJson('api/company');

        $response
            ->assertNotFound()
            ->assertJsonPath('message', __('response.companyNotConfigured'));
    });

    it('responds with valid api data structure', function() {
        $company = Company::factory()
            ->create();
            
        $response = actingAsApiUser()
            ->getJson('api/company');

        $response
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) =>
                $json->has('timestamp')
                    ->has('message')
                    ->has('data', fn(AssertableJson $json) => 
                        $json->whereType('id', 'integer')
                            ->whereType('name', 'string')
                            ->whereType('address', 'string')
                            ->whereType('postalCode', 'string')
                            ->whereType('cityId', 'integer')
                            ->whereType('provinceId', 'integer')
                            ->whereType('countryId', 'integer')
                            ->has('coordinates', fn(AssertableJson $json) =>
                                $json->whereType('latitude', 'double')
                                    ->whereType('longitude', 'double')
                            )
                    )
            );
    });

    it('returns an error on unauthorized request', function() {
        $this->getJson('/api/company')
            ->assertUnauthorized();
    });
});

describe('update', function() {
    it('creates a new company if not configured', function() {
        $company = Company::factory()->make();

        $response = actingAsApiUser(PermissionType::COMPANY_MANAGE)
            ->putJson('api/company', [
                'name' => $company->name,
                'address' => $company->address->address,
                'postalCode' => $company->address->postal_code,
                'cityId' => $company->address->city_id,
                'provinceId' => $company->address->city->province_id,
                'countryId' => $company->address->city->province->country_id,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', __('response.success'));
            
        $this->assertDatabaseHas('company', ['name' => $company->name])
            ->assertDatabaseCount('company', 1);
    });

    it('updates a configured company', function() {
        $company = Company::factory()->create();
        $newName = 'New Company';

        $response = actingAsApiUser(PermissionType::COMPANY_MANAGE)
            ->putJson('api/company', [
                'id' => $company->id,
                'name' => $newName,
                'address' => $company->address->address,
                'postalCode' => $company->address->postal_code,
                'cityId' => $company->address->city_id,
                'provinceId' => $company->address->city->province_id,
                'countryId' => $company->address->city->province->country_id,
            ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', __('response.success'));
            
        $this->assertDatabaseHas('company', ['name' => $newName])
            ->assertDatabaseMissing('company', ['name' => $company->name])
            ->assertDatabaseCount('company', 1);
    });

    it('has proper validation rules', function() {
        expect(new CompanyRequest()->rules())->toMatchSnapshot();
    });

    it('returns an error for user without required permissions', function() {
        $response = actingAsApiUser()
            ->putJson('api/company');

        $response
            ->assertForbidden();
    });

    it('returns an error on unauthorized request', function() {
        $this->putJson('/api/company')
            ->assertUnauthorized();
    });
});
