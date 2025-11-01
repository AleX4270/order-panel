<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model {
    protected $fillable = [
        'symbol', 
    ];

    public function translations(): HasMany {
        return $this->hasMany(CountryTranslation::class, 'country_id');
    }

    public function provinces(): HasMany {
        return $this->hasMany(Province::class, 'country_id');
    }
}
