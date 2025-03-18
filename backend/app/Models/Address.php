<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model {
    protected $table = 'address';

    protected $fillable = [
        'city_id', 
        'address'
    ];

    public function clients(): HasMany {
        return $this->hasMany(Client::class, 'address_id');
    }

    public function city(): BelongsTo {
        return $this->belongsTo(City::class, 'city_id');
    }
}
