<?php
declare(strict_types=1);

namespace App\Services\Api\Priority;

use App\Dtos\Api\Priority\PriorityFilterDto;
use App\Models\Priority;
use Illuminate\Database\Eloquent\Collection;

class PriorityService {
    public function index(PriorityFilterDto $dto): Collection {
        $query = Priority::query()
            ->from('priorities as p')
            ->select([
                'id',
                'symbol',
                'is_active',
            ])
            ->with([
                'translations' => function($query) {
                    $query->whereHas('language', fn($language) => $language->where('symbol', 'pl'))
                    ->select(['name', 'priority_id']);
                }
            ])
            ->active()
            ->get();

        return $query;
    }
}
