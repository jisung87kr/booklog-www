<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingProcess extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function bookcase()
    {
        return $this->belongsTo(BookCase::class, 'book_user_id', 'id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable', 'commentable_type', 'commentable_id');
    }
}
