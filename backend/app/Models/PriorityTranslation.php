<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriorityTranslation extends Model {
    protected $table = 'priority_translation';

    protected $fillable = [
        'priority_id', 
        'language_id', 
        'name',
        'description'
    ];

    public function language(): BelongsTo {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
