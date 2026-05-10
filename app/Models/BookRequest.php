<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRequest extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'book_id', 'user_id', 'student_name', 'student_id_num',
        'status', 'req_date', 'action_date', 'return_date',
        'fine', 'fine_paid', 'approved_by',
    ];

    protected $casts = [
        'req_date' => 'datetime',
        'action_date' => 'datetime',
        'return_date' => 'datetime',
        'fine' => 'decimal:2',
        'fine_paid' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
