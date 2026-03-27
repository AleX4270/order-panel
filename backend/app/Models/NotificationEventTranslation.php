<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationEventTranslation extends Model {
    protected $fillable = [
        'notification_event_id', 
        'language_id', 
        'name'
    ];

    public function notificationEvent(): BelongsTo {
        return $this->belongsTo(NotificationEvent::class, 'notification_event_id');
    }

    public function language(): BelongsTo {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
