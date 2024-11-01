<?php
namespace App\Enums;

// SNS 유저 액션 상수 정의
enum UserActionEnum: string
{
    case BOOK_LIKE = 'book_like';
    case BOOK_SHARE = 'book_share';
    case READING_PROCESS_LIKE = 'reading_process_like';
    case READING_PROCESS_SHARE = 'reading_process_share';
    case READING_PROCESS_REPORT = 'reading_process_report';
    case COMMENT_REPORT = 'comment_report';
    case POST_LIKE = 'post_like';

    public function label(): string
    {
        return match ($this) {
            UserActionEnum::BOOK_LIKE => '좋아요',
            UserActionEnum::BOOK_SHARE => '공유',
            UserActionEnum::READING_PROCESS_LIKE => '피드 좋아요',
            UserActionEnum::READING_PROCESS_SHARE => '피드 공유',
            UserActionEnum::READING_PROCESS_REPORT => '피드 신고',
            UserActionEnum::COMMENT_REPORT => '댓글 신고',
            UserActionEnum::POST_LIKE => '게시물 좋아요',
        };
    }
}
