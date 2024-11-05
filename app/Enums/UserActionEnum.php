<?php
namespace App\Enums;

// SNS 유저 액션 상수 정의
enum UserActionEnum: string
{
    case Bookmark = 'bookmark';
    case LIKE = 'like';
    case UNINTERESTED = 'uninterested';
    case SHARE = 'share';
    case BLOCK = 'block';
    case REPORT = 'report';
    case SHOW_PROFILE = 'show_profile';
    case FOLLOW = 'follow';

    public function label(): string
    {
        return match ($this) {
            UserActionEnum::Bookmark => '저장',
            UserActionEnum::LIKE => '좋아요',
            UserActionEnum::UNINTERESTED => '관심없음',
            UserActionEnum::SHARE => '공유',
            UserActionEnum::BLOCK => '차단',
            UserActionEnum::REPORT => '신고',
            UserActionEnum::SHOW_PROFILE => '프로필 보기',
            UserActionEnum::FOLLOW => '팔로우',
        };
    }
}
