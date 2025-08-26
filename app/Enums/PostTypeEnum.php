<?php
namespace App\Enums;
enum PostTypeEnum: string
{
    case FEED = 'feed';
    case POST = 'post';
    case BOOKCASE = 'bookcase';
    case PAGE = 'page';
    case AD = 'ad';

    public function label()
    {
        return match ($this) {
            PostTypeEnum::FEED => '피드',
            PostTypeEnum::POST => '포스트',
            PostTypeEnum::BOOKCASE => '책장',
            PostTypeEnum::PAGE => '페이지',
            PostTypeEnum::AD => '광고',
        };
    }
}
