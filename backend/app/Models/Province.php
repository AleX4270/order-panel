<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model {
    protected $table = 'province';

    protected $fillable = [
        'country_id', 
        'name', 
    ];
}
