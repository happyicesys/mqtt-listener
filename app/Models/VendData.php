<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendData extends Model
{
    protected $fillable = [
        'connection',
        'ip_address',
        'topic',
        'type',
        'raw',
        'value',
        'vend_code',
    ];

    protected $casts = [
        'raw' => 'json',
        'value' => 'json',
    ];

}
