<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriorityTranslation extends Model {
    protected $table = 'priority_translation';

    protected $fillable = [
        'priority_id', 
        'language_id', 
        'name',
        'description'
    ];
}
