<?php
namespace App\Enums;
enum CategoryTypeEnum: string
{
    case BOOK = 'book';
    case AUTHOR = 'author';

    public function label()
    {
        return match($this){
            CategoryTypeEnum::BOOK => '도서',
            CategoryTypeEnum::AUTHOR => '작가',
        };
    }
}
