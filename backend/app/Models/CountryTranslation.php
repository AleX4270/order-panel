<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CountryTranslation extends Model {
    protected $table = 'country_translation';

    protected $fillable = [
        'country_id', 
        'language_id', 
        'name'
    ];

    public function language(): BelongsTo {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function country(): BelongsTo {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
