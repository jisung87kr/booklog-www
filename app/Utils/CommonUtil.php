<?php
namespace App\Utils;

use App\Services\Crawler\DTOs\BookDetailDTO;

class CommonUtil{
    public static function getMaxPage($total, $perPage){
        return ceil($total / $perPage);
    }

    public static function convertAladinItemToBookDTO($item)
    {
        return new BookDetailDTO(
            id: null,
            title: $item['title'],
            author:  $item['author'],
            isbn: $item['isbn'],
            link: $item['link'],
            type: $item['type'],
            description: $item['description'] ?? null,
            publisher: $item['publisher'] ?? null,
            publishDate: $item['pubDate'] ?? null,
            productId: $item['itemId'] ?? null,
            salePrice: isset($item['priceSales']) ? (float)$item['priceSales'] : null,
            price: isset($item['priceStandard']) ? (float)$item['priceStandard'] : null,
            coverImage: $item['cover'] ?? null,
            categories: [$item['categoryId'] ?? null],
            totalPages: $item['subInfo']['itemPage'] ?? null
        );
    }
}
