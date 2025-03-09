<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model {
    protected $table = 'address';

    protected $fillable = [
        'city_id', 
        'address'
    ];
}
