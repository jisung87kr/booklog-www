<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        'name',
        'email',
        'password',
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

    public function books()
    {
        return $this->belongsToMany(Book::class)->withPivot(['created_at', 'updated_at', 'idx'])->withTimestamps();
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
}
