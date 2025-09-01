<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $with = ['user'];
    protected $appends = ['created_at_human'];

//    protected static function booted()
//    {
//        static::addGlobalScope('withTrashed', function ($builder) {
//            $builder->withTrashed();
//        });
//    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id', 'id');
    }

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffforhumans();
    }

    public function getBodyAttribute($value)
    {
        if ($this->trashed()) {
            return '삭제된 댓글입니다.';
        }
        return $value;
    }
}
