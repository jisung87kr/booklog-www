<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingProcess extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['tags', 'images', 'user'];
    protected $appends = ['created_at_human'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function bookcase()
    {
        return $this->belongsTo(BookCase::class, 'book_user_id', 'id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable', 'commentable_type', 'commentable_id');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable', 'imageable_type', 'imageable_id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffforhumans();
    }
}
