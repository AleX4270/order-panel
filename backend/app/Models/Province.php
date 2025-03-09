<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model {
    protected $table = 'province';

    protected $fillable = [
        'country_id', 
        'name', 
    ];
}
