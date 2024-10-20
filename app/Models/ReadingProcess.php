<?php

namespace App\Models;

use App\Enums\UserActionEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReadingProcess extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $with = ['tags', 'images', 'user'];
    protected $appends = ['created_at_human', 'like_id'];

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

    public function getLikeIdAttribute()
    {
        return $this->attributes['like_id'] ?? null;
    }

    // withLikes 스코프 정의
    public function scopeWithLikes($query)
    {
        $userId = auth()->id();

        // 서브쿼리를 사용하여 like_id를 가져옴
        $query->addSelect([
            'like_id' => DB::table('user_actions')
                ->select('id')
                ->whereColumn('user_actions.user_actionable_id', 'reading_processes.id')
                ->where('user_actions.user_id', $userId)
                ->where('user_actions.user_actionable_type', ReadingProcess::class)
                ->where('user_actions.action', UserActionEnum::READING_PROCESS_LIKE)
                ->limit(1) // 한 개의 좋아요만 가져옴
        ]);
    }
}
