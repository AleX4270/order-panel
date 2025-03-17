<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusTranslation extends Model {
    protected $table = 'order_translation';

    protected $fillable = [
        'order_id',
        'language_id',
        'name',
        'remarks',
        'description'
    ];

    public function language(): BelongsTo {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
