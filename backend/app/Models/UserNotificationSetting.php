<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\ConvertsModelKeysToCamelCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationSetting extends Model {
    use ConvertsModelKeysToCamelCase;

    protected $fillable = [
        'user_id',
        'notification_event_id',
        'notification_channel_id'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notificationEvent(): BelongsTo {
        return $this->belongsTo(NotificationEvent::class, 'notification_event_id');
    }

    public function notificationChannel(): BelongsTo {
        return $this->belongsTo(NotificationChannel::class, 'notification_channel_id');
    }
}
