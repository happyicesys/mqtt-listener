<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendData extends Model
{
    protected $fillable = [
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
