<?php
namespace App\Enums;
enum PostStatusEnum: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case UNPUBLISHED = 'unpublished';

    public function label(){
        return match($this){
            PostStatusEnum::DRAFT => '초안',
            PostStatusEnum::PUBLISHED => '공개됨',
            PostStatusEnum::UNPUBLISHED => '비공개됨',
        };
    }
}
