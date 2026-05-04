<?php
declare(strict_types=1);

namespace App\Services\Api\Company;

use App\Dtos\Api\Address\AddressResolveDto;
use App\Dtos\Api\Company\CompanyDto;
use App\Models\Company;
use App\Services\Api\Address\AddressService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyService {
    public function __construct(
        private readonly AddressService $addressService,
    ) {}

    public function show(): Company {
        return Company::current();        
    }

    public function update(CompanyDto $dto): bool {
        DB::beginTransaction();
        try {
            $company = $dto->isConfigured() ? Company::current() : new Company;

            $address = $this->addressService->findOrCreate(AddressResolveDto::fromArray([
                'address' => $dto->addressDto->address,
                'postalCode' => $dto->addressDto->postalCode,
                'cityId' => $dto->addressDto->cityId,
                'cityName' => $dto->addressDto->cityName,
                'provinceId' => $dto->addressDto->provinceId,
            ]));

            $company->name = $dto->name;
            $company->address_id = $address->id;

            $company->save();

            DB::commit();
            return true;
        }
        catch(Exception $e) {
            Log::error($e);
            DB::rollBack();
            throw $e;
        }
    }
}
