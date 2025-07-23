<?php

namespace App\Services\Crawler;

use App\Services\Crawler\Contracts\BookCrawlerInterface;
use InvalidArgumentException;

class BookCrawlerFactory
{
    /**
     * 사용 가능한 크롤러 목록
     */
    private const AVAILABLE_CRAWLERS = [
        'aladin' => AladinService::class,
        'kyobo' => KyoboBookService::class,
    ];

    /**
     * 크롤러 인스턴스 생성
     *
     * @param string $crawlerName 크롤러 이름 (aladin, kyobo)
     * @return BookCrawlerInterface
     * @throws InvalidArgumentException
     */
    public static function create(string $crawlerName): BookCrawlerInterface
    {
        $crawlerName = strtolower($crawlerName);

        if (!array_key_exists($crawlerName, self::AVAILABLE_CRAWLERS)) {
            throw new InvalidArgumentException("지원하지 않는 크롤러입니다: {$crawlerName}");
        }

        $crawlerClass = self::AVAILABLE_CRAWLERS[$crawlerName];
        return new $crawlerClass();
    }

    /**
     * 사용 가능한 크롤러 목록 반환
     *
     * @return array
     */
    public static function getAvailableCrawlers(): array
    {
        return array_keys(self::AVAILABLE_CRAWLERS);
    }

    /**
     * 모든 크롤러 인스턴스 반환
     *
     * @return array<string, BookCrawlerInterface>
     */
    public static function createAll(): array
    {
        $crawlers = [];
        foreach (self::AVAILABLE_CRAWLERS as $name => $class) {
            $crawlers[$name] = new $class();
        }
        return $crawlers;
    }
}