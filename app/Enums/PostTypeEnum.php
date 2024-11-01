<?php
namespace App\Enums;
enum PostTypeEnum: string
{
    case POST = 'post';
    case PAGE = 'page';
    case AD = 'ad';

    public function label()
    {
        return match ($this) {
            PostTypeEnum::POST => '포스트',
            PostTypeEnum::PAGE => '페이지',
            PostTypeEnum::AD => '광고',
        };
    }
}
