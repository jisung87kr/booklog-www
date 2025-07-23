<?php

namespace App\Services\Crawler\DTOs;

class BookCategoryRequestDTO
{
    public readonly string $categoryId;
    public readonly int $page;
    public readonly int $limit;

    public function __construct(
        string $categoryId,
        int $page = 1,
        int $limit = 20
    ) {
        $this->categoryId = trim($categoryId);
        $this->page = max(1, $page);
        $this->limit = max(1, min(100, $limit)); // 최대 100개로 제한
    }

    public function toArray(): array
    {
        return [
            'categoryId' => $this->categoryId,
            'page' => $this->page,
            'limit' => $this->limit,
        ];
    }
}