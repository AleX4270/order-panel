<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model {
    protected $table = 'province';

    protected $fillable = [
        'country_id', 
        'name', 
    ];

    public function country(): BelongsTo {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function cities(): HasMany {
        return $this->hasMany(City::class, 'province_id');
    }
}
