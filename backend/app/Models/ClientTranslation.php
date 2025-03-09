<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientTranslation extends Model {
    protected $table = 'client_translation';

    protected $fillable = [
        'client_id', 
        'language_id', 
        'description'
    ];
}
