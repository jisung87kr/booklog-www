<?php
namespace App\Enums;
enum CategoryTypeEnum: string
{
    case POST = 'post';
    case AD = 'ad';

    public function label()
    {
        return match ($this) {
            CategoryTypeEnum::POST => '포스트',
            CategoryTypeEnum::AD => '광고',
        };
    }
}
