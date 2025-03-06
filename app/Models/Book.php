<?php

namespace App\Models;

use App\Enums\UserActionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Book extends Model
{
    use HasFactory;

    protected $appends = ['book_id'];

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
                        ->whereColumn('user_actions.user_actionable_id', 'books.id')
                        ->where('user_actions.user_id', auth()->id())
                        ->where('user_actions.user_actionable_type', Book::class)
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
                        ->whereColumn('user_actions.user_actionable_id', 'books.id')
                        ->where('user_actions.user_actionable_type', Book::class)
                        ->where('user_actions.action', $item)
                ]);
            }

            $builder->addSelect([
                'comment_count' => DB::table('comments')
                    ->selectRaw('count(*)')
                    ->whereColumn('comments.commentable_id', 'books.id')
                    ->where('comments.commentable_type', Book::class)
            ]);

        });
    }


    protected $guarded = [];

//    public function users()
//    {
//        return $this->belongsToMany(User::class);
//    }
//

    public function bookcases()
    {
        return $this->belongsToMany(UserBookcase::class, 'book_user_bookcase', 'book_id', 'bookcase_id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_book', 'book_id', 'author_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function getBookIdAttribute()
    {
        return $this->id;
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['q'] ?? false, function (Builder $query, $q) {
            $sanitize = preg_replace('/[^a-zA-Z0-9가-힣\s]/', '', $q);
            $query->where('title', 'like', '%'.$q.'%')
                ->orWhereRaw("REGEXP_REPLACE(title, '[^a-zA-Z0-9가-힣]', '') LIKE ?", ["%{$sanitize}%"])
                ->orWhere('isbn', $q);
        });
    }
}
