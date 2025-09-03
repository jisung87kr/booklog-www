<?php
namespace App\Enums;

enum ContactCategoryEnum: string
{
    case GENERAL = 'general';
    case TECHNICAL = 'technical';
    case PARTNERSHIP = 'partnership';
    case BUG = 'bug';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            ContactCategoryEnum::GENERAL => '일반 문의',
            ContactCategoryEnum::TECHNICAL => '기술 문의',
            ContactCategoryEnum::PARTNERSHIP => '제휴 문의',
            ContactCategoryEnum::BUG => '버그 신고',
            ContactCategoryEnum::OTHER => '기타',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}