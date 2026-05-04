<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model {
    protected $table = 'company';
    protected $fillable = [
        'name', 
        'address_id', 
    ];

    public static function current(): Company {
        return static::orderBy('id')->firstOrFail();
    }

    public function address(): BelongsTo {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
