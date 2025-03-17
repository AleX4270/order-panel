<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTranslation extends Model {
    protected $table = 'order_translation';

    protected $fillable = [
        'order_id',
        'language_id',
        'name',
        'remarks',
        'description',
    ];
}
