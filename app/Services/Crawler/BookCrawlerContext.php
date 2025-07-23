<?php

namespace App\Services\Crawler;

use App\Services\Crawler\Contracts\BookCrawlerInterface;
use App\Services\Crawler\DTOs\BookSearchRequestDTO;
use App\Services\Crawler\DTOs\BookCategoryRequestDTO;
use App\Services\Crawler\DTOs\BookDetailRequestDTO;
use App\Services\Crawler\DTOs\AllCategoryRequestDTO;
use App\Services\Crawler\DTOs\BookResponseDTO;

class BookCrawlerContext
{
    private BookCrawlerInterface $crawler;

    public function __construct(BookCrawlerInterface $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * 크롤러 전략 변경
     *
     * @param BookCrawlerInterface $crawler
     * @return void
     */
    public function setCrawler(BookCrawlerInterface $crawler): void
    {
        $this->crawler = $crawler;
    }

    /**
     * 현재 사용중인 크롤러 반환
     *
     * @return BookCrawlerInterface
     */
    public function getCrawler(): BookCrawlerInterface
    {
        return $this->crawler;
    }

    /**
     * 키워드로 도서 검색
     *
     * @param BookSearchRequestDTO $request
     * @return BookResponseDTO
     */
    public function searchBooks(BookSearchRequestDTO $request): BookResponseDTO
    {
        return $this->crawler->searchBooks($request);
    }

    /**
     * 카테고리별 도서 목록 조회
     *
     * @param BookCategoryRequestDTO $request
     * @return BookResponseDTO
     */
    public function getBooksByCategory(BookCategoryRequestDTO $request): BookResponseDTO
    {
        return $this->crawler->getBooksByCategory($request);
    }

    /**
     * 도서 상세정보 조회
     *
     * @param BookDetailRequestDTO $request
     * @return BookResponseDTO
     */
    public function getBookDetail(BookDetailRequestDTO $request): BookResponseDTO
    {
        return $this->crawler->getBookDetail($request);
    }

    /**
     * 모든 카테고리의 도서 목록 조회
     *
     * @param AllCategoryRequestDTO $request
     * @return BookResponseDTO
     */
    public function getAllBooksByCategory(AllCategoryRequestDTO $request): BookResponseDTO
    {
        return $this->crawler->getAllBooksByCategory($request);
    }

    /**
     * 크롤러 정보 반환
     *
     * @return array
     */
    public function getCrawlerInfo(): array
    {
        return [
            'name' => $this->crawler->getName(),
            'maxResults' => $this->crawler->getMaxResults(),
            'maxTotalResults' => $this->crawler->getMaxTotalResults(),
        ];
    }
}