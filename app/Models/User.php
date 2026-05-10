<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'student_number', 'email', 'password', 'role', 'phone', 'address', 'profile_image', 'email_verified',
        'verification_token', 'reset_token', 'reset_expires',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
    ];

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image && ! in_array($this->profile_image, ['default.png', 'imagess.png'])) {
            return asset('storage/'.$this->profile_image);
        }

        return asset('imagess.png');
    }
}
