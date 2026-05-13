<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'student_number', 'email', 'password', 'role', 'phone', 'address', 'profile_image', 'email_verified',
        'verification_token', 'reset_token', 'reset_expires', 'google2fa_secret', 'google2fa_enabled',
        'login_otp', 'login_otp_expires',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
        'reset_token',
        'verification_token',
    ];

    protected $casts = [
        'email_verified' => 'boolean',
        'google2fa_enabled' => 'boolean',
    ];

    public function getProfileImageUrlAttribute(): string
    {
        if ($this->profile_image && ! in_array($this->profile_image, ['default.png', 'imagess.png'])) {
            return asset('storage/'.$this->profile_image);
        }

        return asset('imagess.png');
    }
}
