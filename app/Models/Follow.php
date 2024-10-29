<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Follow extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['followerUser', 'followingUser'];
    protected $appends = ['created_at_human'];

    protected static function booted()
    {
        static::addGlobalScope('is_following', function (Builder $query) {
            $userId = auth()->id();
            $query->select('follows.*');
            $query->addSelect([
                //DB::raw("(SELECT id FROM follows WHERE follows.follow_id = follows.following_id AND follows.following_id = follows.follow_id AND follows.follow_id = " . auth()->id() . " LIMIT 1) AS is_following")
                DB::raw("(SELECT id
                                    FROM follows AS f2
                                    WHERE f2.following_id = follows.follow_id
                                      AND f2.follow_id = '{$userId}'
                                    LIMIT 1) AS is_following")
            ]);
        });
    }

    public function followerUser()
    {
        return $this->belongsTo(User::class, 'follow_id', 'id');
    }

    public function followingUser()
    {
        return $this->belongsTo(User::class, 'following_id', 'id');
    }

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffforhumans();
    }

//    public function scopeIsFollowing($query)
//    {
//        // 맞팔로우 하는지 확인하는 서브 쿼리 속성
//        $query->addSelect([
//            'is_following' => Follow::select('id')
//                ->whereColumn('follow_id', 'users.id')
//                ->where('following_id', auth()->id())
//                ->limit(1)
//        ]);
//    }
}
