<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';

    protected $fillable = ['key', 'value'];

    public $timestamps = false;

    public static function get($key, $default = null)
    {
        $config = self::where('key', $key)->first();

        return $config ? $config->value : $default;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
