<?php
declare(strict_types=1);

namespace App\Models;

use App\Traits\ConvertsModelKeysToCamelCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationEvent extends Model {
    use ConvertsModelKeysToCamelCase;

    protected $fillable = [
        'symbol',
        'is_active'
    ];

    public function translations(): HasMany {
        return $this->hasMany(NotificationEventTranslation::class, 'notification_event_id');
    }

    public function userNotificationSettings(): HasMany {
        return $this->hasMany(UserNotificationSetting::class, 'notification_event_id');
    }
}
