<?php
declare(strict_types=1);

namespace App\Services\Api\NotificationChannel;

use App\Dtos\Api\NotificationChannel\NotificationChannelFilterDto;
use App\Repositories\NotificationChannelRepository;
use Illuminate\Support\Collection;

class NotificationChannelService {
    public function __construct(
        private readonly NotificationChannelRepository $notificationChannelRepository,
    ) {}

    public function index(NotificationChannelFilterDto $dto): Collection {
        $query = $this->notificationChannelRepository->getAll($dto);
        return $query->get();
    }
}
