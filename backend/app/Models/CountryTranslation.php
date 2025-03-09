<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model {
    protected $table = 'country_translation';

    protected $fillable = [
        'country_id', 
        'language_id', 
        'name'
    ];
}
