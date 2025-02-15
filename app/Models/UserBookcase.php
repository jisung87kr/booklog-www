<?php

namespace App\Models;

use App\Enums\UserActionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class UserBookcase extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['formatted_created_at'];
    protected $with = ['user', 'books'];

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
                        ->whereColumn('user_actions.user_actionable_id', 'user_bookcases.id')
                        ->where('user_actions.user_id', auth()->id())
                        ->where('user_actions.user_actionable_type', UserBookcase::class)
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
                        ->whereColumn('user_actions.user_actionable_id', 'user_bookcases.id')
                        ->where('user_actions.user_actionable_type', UserBookcase::class)
                        ->where('user_actions.action', $item)
                ]);
            }

            $builder->addSelect([
                'comment_count' => DB::table('comments')
                    ->selectRaw('count(*)')
                    ->whereColumn('comments.commentable_id', 'user_bookcases.id')
                    ->where('comments.commentable_type', UserBookcase::class)
            ]);

        });
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_user_bookcase', 'bookcase_id', 'book_id')->withPivot(['order'])->withTimestamps()->orderBy('order');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }
}
