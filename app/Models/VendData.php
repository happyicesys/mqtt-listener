<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendData extends Model
{
    protected $fillable = [
        'type',
        'value',
        'vend_code',
    ];

    protected $casts = [
        'value' => 'json',
    ];

}
