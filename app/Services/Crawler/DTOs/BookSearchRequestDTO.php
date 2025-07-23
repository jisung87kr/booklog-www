<?php

namespace App\Services\Crawler\DTOs;

class BookSearchRequestDTO
{
    public readonly string $query;
    public readonly int $page;
    public readonly int $limit;

    public function __construct(
        string $query,
        int $page = 1,
        int $limit = 20
    ) {
        $this->query = trim($query);
        $this->page = max(1, $page);
        $this->limit = max(1, min(100, $limit)); // 최대 100개로 제한
    }

    public function toArray(): array
    {
        return [
            'query' => $this->query,
            'page' => $this->page,
            'limit' => $this->limit,
        ];
    }
}