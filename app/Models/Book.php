<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn', 'title', 'category', 'status', 'image', 'stock', 'author',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function requests()
    {
        return $this->hasMany(BookRequest::class, 'book_id');
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/'.$this->image);
        }

        return null;
    }
}
