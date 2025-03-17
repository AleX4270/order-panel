<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientTranslation extends Model {
    protected $table = 'client_translation';

    protected $fillable = [
        'client_id', 
        'language_id', 
        'description'
    ];

    public function language(): BelongsTo {
        return $this->belongsTo(Language::class, 'language_id');
    }
}
