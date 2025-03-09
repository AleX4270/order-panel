<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model {
    protected $table = 'city';

    protected $fillable = [
        'province_id', 
        'postal_code', 
        'name'
    ];
}
