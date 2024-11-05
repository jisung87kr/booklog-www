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

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('withActions', function (Builder $builder) {
            $builder->select('*')
                ->addSelect([
                    'bookmark_id' => DB::table('user_actions')
                        ->select('id')
                        ->whereColumn('user_actions.user_actionable_id', 'posts.id')
                        ->where('user_actions.user_id', auth()->id())
                        ->where('user_actions.user_actionable_type', Post::class)
                        ->where('user_actions.action', UserActionEnum::Bookmark)
                        ->limit(1)
                ])
                ->addSelect([
                    'like_id' => DB::table('user_actions')
                        ->select('id')
                        ->whereColumn('user_actions.user_actionable_id', 'posts.id')
                        ->where('user_actions.user_id', auth()->id())
                        ->where('user_actions.user_actionable_type', Post::class)
                        ->where('user_actions.action', UserActionEnum::LIKE)
                        ->limit(1)
                ])
                ->addSelect([
                    'uninterested_id' => DB::table('user_actions')
                        ->select('i```d')
                        ->whereColumn('user_actions.user_actionable_id', 'posts.id')
                        ->where('user_actions.user_id', auth()->id())
                        ->where('user_actions.user_actionable_type', Post::class)
                        ->where('user_actions.action', UserActionEnum::UNINTERESTED)
                        ->limit(1)
                ])
                ->addSelect([
                    'share_id' => DB::table('user_actions')
                        ->select('id')
                        ->whereColumn('user_actions.user_actionable_id', 'posts.id')
                        ->where('user_actions.user_id', auth()->id())
                        ->where('user_actions.user_actionable_type', Post::class)
                        ->where('user_actions.action', UserActionEnum::SHARE)
                        ->limit(1)
                ])
                ->addSelect([
                    'block_id' => DB::table('user_actions')
                        ->select('id')
                        ->whereColumn('user_actions.user_actionable_id', 'posts.id')
                        ->where('user_actions.user_id', auth()->id())
                        ->where('user_actions.user_actionable_type', Post::class)
                        ->where('user_actions.action', UserActionEnum::BLOCK)
                        ->limit(1)
                ])
                ->addSelect([
                    'report_id' => DB::table('user_actions')
                        ->select('id')
                        ->whereColumn('user_actions.user_actionable_id', 'posts.id')
                        ->where('user_actions.user_id', auth()->id())
                        ->where('user_actions.user_actionable_type', Post::class)
                        ->where('user_actions.action', UserActionEnum::REPORT)
                        ->limit(1)
                ])
                ->addSelect([
                    'show_profile_id' => DB::table('user_actions')
                        ->select('id')
                        ->whereColumn('user_actions.user_actionable_id', 'posts.id')
                        ->where('user_actions.user_id', auth()->id())
                        ->where('user_actions.user_actionable_type', Post::class)
                        ->where('user_actions.action', UserActionEnum::SHOW_PROFILE)
                        ->limit(1)
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
