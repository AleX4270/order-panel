<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriorityTranslation extends Model {
    protected $fillable = [
        'priority_id', 
        'language_id', 
        'name',
        'description'
    ];

    public function language(): BelongsTo {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function priority(): BelongsTo {
        return $this->belongsTo(Priority::class, 'priority_id');
    }
}
