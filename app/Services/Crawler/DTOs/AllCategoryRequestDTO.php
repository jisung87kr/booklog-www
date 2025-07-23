<?php

namespace App\Services\Crawler\DTOs;

class AllCategoryRequestDTO
{
    public readonly string $categoryId;

    public function __construct(string $categoryId)
    {
        $this->categoryId = trim($categoryId);
    }

    public function toArray(): array
    {
        return [
            'categoryId' => $this->categoryId,
        ];
    }
}