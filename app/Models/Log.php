<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $primaryKey = 'log_id';

    public $incrementing = true;

    protected $fillable = ['description', 'timestamp'];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public $timestamps = false;
}
