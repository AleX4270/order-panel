<?php

namespace App\Models;

use App\Traits\ConvertsModelKeysToCamelCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderRequest extends Model {
    use SoftDeletes, ConvertsModelKeysToCamelCase;

    protected $fillable = [
        'client_id',
        'address_id',
        'remarks',
        'ip_address',
        'user_agent',
        'consent_given_at',
    ];

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function address(): BelongsTo {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
