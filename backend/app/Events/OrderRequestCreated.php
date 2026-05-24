<?php
declare(strict_types=1);

namespace App\Events;

use App\Models\OrderRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderRequestCreated {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public OrderRequest $orderRequest
    ) {}
}
