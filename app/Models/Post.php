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
    protected $with = ['attachments', 'user', 'images'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('withActions', function (Builder $builder) {
            $builder->select('*');

            $actions = [
                'bookmark_id' => UserActionEnum::Bookmark,
                'like_id' => UserActionEnum::LIKE,
                'uninterested_id' => UserActionEnum::UNINTERESTED,
                'share_id' => UserActionEnum::SHARE,
                'block_id' => UserActionEnum::BLOCK,
                'report_id' => UserActionEnum::REPORT,
                'show_profile_id' => UserActionEnum::SHOW_PROFILE,
            ];

            foreach ($actions as $alias => $action) {
                $builder->addSelect([
                    $alias => DB::table('user_actions')
                        ->select('id')
                        ->whereColumn('user_actions.user_actionable_id', 'posts.id')
                        ->where('user_actions.user_id', auth()->id())
                        ->where('user_actions.user_actionable_type', Post::class)
                        ->where('user_actions.action', $action)
                        ->limit(1)
                ]);
            }

            $actionCount = [
              'like_count' => UserActionEnum::LIKE,
              'share_count' => UserActionEnum::SHARE,
            ];

            foreach ($actionCount as $index => $item) {
                $builder->addSelect([
                    $index => DB::table('user_actions')
                        ->selectRaw('count(*)')
                        ->whereColumn('user_actions.user_actionable_id', 'posts.id')
                        ->where('user_actions.user_actionable_type', Post::class)
                        ->where('user_actions.action', $item)
                ]);
            }

            $builder->addSelect([
                'comment_count' => DB::table('comments')
                    ->selectRaw('count(*)')
                    ->whereColumn('comments.commentable_id', 'posts.id')
                    ->where('comments.commentable_type', Post::class)
            ]);

        });
    }

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

        $query->when($filters['user_id'] ?? null, function ($query, $userId) {
            $query->where('user_id', $userId);
        });

        $query->when($filters['q'] ?? null, function ($query, $q) {
            $query->where('content', 'like', "%{$q}%");
        });
    }
}
