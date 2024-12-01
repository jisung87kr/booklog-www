<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserActionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'introduction',
        'link',
        'is_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    protected static function booted()
    {
        $userId = auth()->id();
        static::addGlobalScope('withFollowersCount', function (Builder $builder) use ($userId) {
            $builder->select([
               'users.*',
               DB::raw('(SELECT COUNT(*) FROM follows WHERE follows.following_id = users.id) as followers_count'),
               DB::raw("IF((SELECT id FROM follows WHERE following_id = users.id AND follow_id = '{$userId}') IS NOT NULL, true, false) AS is_following")
            ]);

            //TODO http://booklog.test:9499/api/feeds?page=1 에서 쿼리 오류남.
            // 프로필에서는 문제 없음
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
                        ->select('user_actions.id')
                        ->whereColumn('user_actions.user_actionable_id', 'users.id')
                        ->where('user_actions.user_id', $userId)
                        ->where('user_actions.user_actionable_type', User::class)
                        ->where('user_actions.action', $action)
                        ->limit(1)
                ]);
            }
        });
    }

    public function bookcases()
    {
        return $this->hasMany(UserBookcase::class);
    }

    public function books()
    {
        return $this->hasManyThrough(
            Book::class,
            UserBookcase::class,
            'user_id',        // UserBookcase에서 User를 참조하는 키
            'id',             // Book에서 Bookcase를 참조하는 키 (중간 테이블 사용)
            'id',             // User에서 기본 키
            'id'              // UserBookcase에서 기본 키
        );
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class)->withPivot(['created_at', 'updated_at'])->withTimestamps();
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function actions()
    {
        return $this->hasMany(UserAction::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'following_id', 'id');
    }

    public function followings()
    {
        return $this->hasMany(Follow::class, 'follow_id', 'id');
    }

    public function mentions()
    {
        return $this->hasMany(Mention::class,  'mentioned_user_id', 'id');
    }

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['q'] ?? false, function (Builder $query, $q) {
            $query->where('username', 'like', '%'.$q.'%');
        });
    }
}
