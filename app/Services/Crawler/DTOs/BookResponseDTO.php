<?php

namespace App\Services\Crawler\DTOs;

class BookResponseDTO
{
    public readonly bool $success;
    public readonly array|BookDetailDTO|null $data;
    public readonly ?string $error;
    public readonly string $source;
    public readonly int $totalCount;
    public readonly int $currentPage;
    public readonly int $totalPages;

    public function __construct(
        bool $success,
        array|BookDetailDTO $data = null,
        ?string $error = null,
        string $source = '',
        int $totalCount = 0,
        int $currentPage = 1,
        int $totalPages = 1
    ) {
        $this->success = $success;
        $this->data = $data;
        $this->error = $error;
        $this->source = $source;
        $this->totalCount = $totalCount;
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
    }

    public static function success(
        array|BookDetailDTO $data,
        string $source = '',
        int $totalCount = 0,
        int $currentPage = 1,
        int $totalPages = 1
    ): self {
        return new self(
            success: true,
            data: $data,
            source: $source,
            totalCount: $totalCount,
            currentPage: $currentPage,
            totalPages: $totalPages
        );
    }

    public static function error(string $error, string $source = ''): self
    {
        return new self(
            success: false,
            error: $error,
            source: $source
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'data' => $this->data,
            'error' => $this->error,
            'source' => $this->source,
            'totalCount' => $this->totalCount,
            'currentPage' => $this->currentPage,
            'totalPages' => $this->totalPages,
        ];
    }
}
