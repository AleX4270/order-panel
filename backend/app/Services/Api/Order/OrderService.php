<?php
declare(strict_types=1);

namespace App\Services\Api\Order;

use App\Dtos\Api\Order\OrderDto;
use Illuminate\Support\Collection;

class OrderService {
    public function index(): Collection {
        return collect([]);
    }

    public function save(OrderDto $dto): bool {
        //Get or create a new address (helper method)
        //Find or create a new client
        //Save the order
        
    }
}
