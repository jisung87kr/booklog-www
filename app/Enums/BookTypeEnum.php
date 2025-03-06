<?php
namespace App\Enums;
enum BookTypeEnum: string
{
    case ALADIN = 'aladin';
    case KYOBO = 'kyobo';
    case NL = 'nl';

    public function label(){
        return match($this){
            BookTypeEnum::ALADIN => '알라딘',
            BookTypeEnum::KYOBO => '교보문고',
            BookTypeEnum::NL => '국립중앙도서관',
        };
    }
}
