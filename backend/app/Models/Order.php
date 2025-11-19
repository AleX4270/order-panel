<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model {
    protected $attributes = [
        'is_active' => 1
    ];

    protected $fillable = [
        'symbol',
        'date_deadline',
        'date_completed',
        'user_creation_id',
        'user_modification_id',
        'priority_id',
        'client_id',
        'status_id',
        'is_active',
        'created_at',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive(Builder $query): void {
        $query->where('is_active', 1);
    }

    public function translations(): HasMany {
        return $this->hasMany(OrderTranslation::class, 'order_id');
    }

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'user_creation_id');
    }

    public function modifier(): BelongsTo {
        return $this->belongsTo(User::class, 'user_modification_id');
    }

    public function priority(): BelongsTo {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function status(): BelongsTo {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }
}
