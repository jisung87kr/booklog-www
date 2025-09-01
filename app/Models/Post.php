<?php

namespace App\Models;

use App\Enums\PostStatusEnum;
use App\Enums\PostTypeEnum;
use App\Enums\UserActionEnum;
use App\Traits\Categorizable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\UserBookcase;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Categorizable;

    protected $guarded = [];
    protected $with = ['attachments', 'user', 'images', 'bookcase', 'parentPost'];
    protected $appends = ['formatted_created_at', 'quote_count'];
    protected $casts = [
        'meta' => 'array',
        'status' => PostStatusEnum::class,
        'type' => PostTypeEnum::class,
        'published_at' => 'datetime',
    ];

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
        return $this->morphMany(Image::class, 'imageable')->orderBy('sort_order');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function mentions()
    {
        return $this->belongsToMany(User::class, 'mentions', 'post_id', 'mentioned_user_id');
    }

    public function scopePublishedFeeds(Builder $query)
    {
        $query->published();
        return $query->whereIn('type', [PostTypeEnum::FEED, PostTypeEnum::BOOKCASE]);
    }

    public function scopePublishedPosts(Builder $query)
    {
        $query->published();
        return $query->whereIn('type', [PostTypeEnum::POST]);
    }

    public function scopePublished(Builder $query){
        return $query->where('status', PostStatusEnum::PUBLISHED);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function scopeFilter(Builder $query, $filters)
    {
        $query->when($filters['type'] ?? null, function ($query, $type) {
            $query->whereIn('type', $type);
        });

        $query->when($filters['user_id'] ?? null, function ($query, $userId) {
            $query->where('user_id', $userId);
        });

        $query->when($filters['username'] ?? null, function ($query, $username) {
            $query->orWhereHas('user', function ($query) use ($username) {
                $query->where('username', 'like', "%{$username}%");
            });
        });

        $query->when($filters['q'] ?? null, function ($query, $q) {
            $query->where('content', 'like', "%{$q}%");
        });
    }

    public function bookcase()
    {
        return $this->hasOne(UserBookcase::class, 'id', 'ref_key');
    }

    public function parentPost()
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    public function originalPost()
    {
        return $this->belongsTo(Post::class, 'original_parent_id');
    }

    public function quotePosts()
    {
        return $this->hasMany(Post::class, 'parent_id');
    }

    public function getQuoteCountAttribute()
    {
        return $this->quotePosts()->count();
    }
}
