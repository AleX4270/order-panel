<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusTranslation extends Model {
    protected $table = 'order_translation';

    protected $fillable = [
        'order_id',
        'language_id',
        'name',
        'remarks',
        'description'
    ];
}
