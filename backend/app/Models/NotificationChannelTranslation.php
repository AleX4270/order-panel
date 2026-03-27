<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationChannelTranslation extends Model {
    protected $fillable = [
        'notification_channel_id', 
        'language_id', 
        'name'
    ];

    public function notificationChannel(): BelongsTo {
        return $this->belongsTo(NotificationChannel::class, 'notification_channel_id');
    }

    public function language(): BelongsTo {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
