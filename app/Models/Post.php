<?php

namespace App\Models;

use App\Enums\PostStatusEnum;
use App\Enums\UserActionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $with = ['attachments', 'comments', 'user', 'images'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function scopePublished(){
        return $this->where('status', PostStatusEnum::PUBLISHED);
    }

    public function scopeFilter(Builder $query, $filters)
    {
        $query->when($filters['type'] ?? null, function ($query, $type) {
            $query->where('type', $type);
        });
    }

    // withLikes 스코프 정의
    public function scopeWithLikes($query)
    {
        $userId = auth()->id();

        // 서브쿼리를 사용하여 like_id를 가져옴
        $query->addSelect([
            'like_id' => DB::table('user_actions')
                ->select('id')
                ->whereColumn('user_actions.user_actionable_id', 'posts.id')
                ->where('user_actions.user_id', $userId)
                ->where('user_actions.user_actionable_type', Post::class)
                ->where('user_actions.action', UserActionEnum::POST_LIKE)
                ->limit(1) // 한 개의 좋아요만 가져옴
        ]);
    }
}
