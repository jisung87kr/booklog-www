<?php

namespace App\Services\Crawler\DTOs;

class BookDetailDTO
{
    public readonly ?string $id;
    public readonly string $title;
    public readonly string $author;
    public readonly string $isbn;
    public readonly string $type;
    public readonly ?string $description;
    public readonly ?string $publisher;
    public readonly ?string $publishDate;
    public readonly ?string $productId;
    public readonly ?float $salePrice;
    public readonly ?float $price;
    public readonly ?string $coverImage;
    public readonly ?string $totalPages;
    public readonly array $categories;


    public function __construct(
        ? string $id,
        string $title,
        string $author,
        string $isbn,
        string $link,
        string $type,
        ?string $description = null,
        ?string $publisher = null,
        ?string $publishDate = null,
        ?string $productId = null,
        ?float $salePrice = null,
        ?float $price = null,
        ?string $coverImage = null,
        ?array $categories = null,
        ?string $totalPages = null,
    ) {
        $this->id = trim($id);
        $this->title = trim($title);
        $this->author = trim($author);
        $this->isbn = trim($isbn);
        $this->link = trim($link);
        $this->type = trim($type);
        $this->description = $description ? trim($description) : null;
        $this->publisher = $publisher ? trim($publisher) : null;
        $this->publishDate = $publishDate ? trim($publishDate) : null;
        $this->productId = $productId ? trim($productId) : null;
        $this->salePrice = $salePrice;
        $this->price = $price;
        $this->coverImage = $coverImage ? trim($coverImage) : null;
        $this->totalPages = $totalPages ? trim($totalPages) : null;
        $this->categories = is_array($categories) ? array_map('trim', $categories) : [];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'link' => $this->link,
            'type' => $this->type,
            'description' => $this->description,
            'publisher' => $this->publisher,
            'publishDate' => $this->publishDate,
            'productId' => $this->productId,
            'salePrice' => $this->salePrice,
            'price' => $this->price,
            'coverImage' => $this->coverImage,
            'totalPages' => $this->totalPages,
            'categories' => $this->categories,
        ];
    }
}
