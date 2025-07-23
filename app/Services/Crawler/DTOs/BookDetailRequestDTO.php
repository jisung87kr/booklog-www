<?php

namespace App\Services\Crawler\DTOs;

class BookDetailRequestDTO
{
    public readonly string $bookId;
    public readonly string $idType;

    public function __construct(
        string $bookId,
        string $idType = 'ISBN'
    ) {
        $this->bookId = trim($bookId);
        $this->idType = strtoupper(trim($idType));
    }

    public function toArray(): array
    {
        return [
            'bookId' => $this->bookId,
            'idType' => $this->idType,
        ];
    }
}