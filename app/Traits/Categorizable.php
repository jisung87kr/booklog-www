<?php

namespace App\Traits;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Categorizable
{
    /**
     * 모델과 카테고리 간의 다형성 다대다 관계
     */
    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    /**
     * 특정 카테고리 할당
     */
    public function assignCategory(Category $category): void
    {
        $this->categories()->attach($category->id);
    }

    /**
     * 특정 카테고리 해제
     */
    public function removeCategory(Category $category): void
    {
        $this->categories()->detach($category->id);
    }

    /**
     * 여러 카테고리 할당 (기존 카테고리 제거 후)
     */
    public function syncCategories(array $categoryIds): void
    {
        $this->categories()->sync($categoryIds);
    }

    /**
     * 카테고리 ID 배열로 할당
     */
    public function assignCategories(array $categoryIds): void
    {
        $this->categories()->attach($categoryIds);
    }

    /**
     * 모든 카테고리 해제
     */
    public function removeAllCategories(): void
    {
        $this->categories()->detach();
    }

    /**
     * 특정 카테고리를 가지고 있는지 확인
     */
    public function hasCategory(Category $category): bool
    {
        return $this->categories()->where('category_id', $category->id)->exists();
    }

    /**
     * 특정 카테고리 ID를 가지고 있는지 확인
     */
    public function hasCategoryId(int $categoryId): bool
    {
        return $this->categories()->where('category_id', $categoryId)->exists();
    }

    /**
     * 특정 타입의 카테고리만 가져오기
     */
    public function categoriesByType(string $type): MorphToMany
    {
        return $this->categories()->where('type', $type);
    }

    /**
     * 활성화된 카테고리만 가져오기
     */
    public function activeCategories(): MorphToMany
    {
        return $this->categories()->where('is_active', true);
    }

    /**
     * 카테고리 이름들을 콤마로 구분된 문자열로 반환
     */
    public function getCategoryNamesAttribute(): string
    {
        return $this->categories->pluck('name')->join(', ');
    }

    /**
     * 특정 타입의 카테고리 이름들을 반환
     */
    public function getCategoryNamesByType(string $type): string
    {
        return $this->categories->where('type', $type)->pluck('name')->join(', ');
    }
}