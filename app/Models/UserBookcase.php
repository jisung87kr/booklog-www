<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBookcase extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['formatted_created_at'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_user_bookcase', 'bookcase_id', 'book_id')->withPivot(['order'])->withTimestamps()->orderBy('order');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
