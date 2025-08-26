<?php

namespace App\Models;

use App\Enums\CategoryTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function child()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    /**
     * 이 카테고리에 속한 포스트들 (다형성 다대다 관계)
     */
    public function posts()
    {
        return $this->morphedByMany('App\Models\Post', 'categorizable');
    }

    /**
     * 이 카테고리를 사용하는 모든 모델들
     */
    public function categorizables()
    {
        return $this->hasMany('App\Models\Categorizable', 'category_id');
    }

    public function scopePost(Builder $query)
    {
        return $query->where('type', CategoryTypeEnum::POST);
    }
}
