<?php
declare(strict_types=1);

namespace App\Services\Api\NotificationEvent;

use App\Dtos\Api\NotificationEvent\NotificationEventFilterDto;
use App\Repositories\NotificationEventRepository;
use Illuminate\Support\Collection;

class NotificationEventService {
    public function __construct(
        private readonly NotificationEventRepository $notificationEventRepository,
    ) {}

    public function index(NotificationEventFilterDto $dto): Collection {
        $query = $this->notificationEventRepository->getAll($dto);
        return $query->get();
    }
}
