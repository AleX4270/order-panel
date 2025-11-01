<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTranslation extends Model {
    protected $fillable = [
        'order_id',
        'language_id',
        'name',
        'remarks',
        'description',
    ];

    public function language(): BelongsTo {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function order(): BelongsTo {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
