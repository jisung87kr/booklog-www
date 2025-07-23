<?php

namespace App\Services\Crawler;

use App\Services\Crawler\Contracts\BookCrawlerInterface;
use App\Services\Crawler\DTOs\BookSearchRequestDTO;
use App\Services\Crawler\DTOs\BookCategoryRequestDTO;
use App\Services\Crawler\DTOs\BookDetailRequestDTO;
use App\Services\Crawler\DTOs\AllCategoryRequestDTO;
use App\Services\Crawler\DTOs\BookResponseDTO;

/**
 * 도서 크롤링 통합 서비스
 * 
 * 여러 크롤러를 통합하여 사용할 수 있는 서비스 클래스
 */
class BookCrawlerService
{
    private BookCrawlerContext $context;
    private array $crawlers;

    public function __construct()
    {
        $this->crawlers = BookCrawlerFactory::createAll();
        // 기본 크롤러를 알라딘으로 설정
        $this->context = new BookCrawlerContext($this->crawlers['aladin']);
    }

    /**
     * 크롤러 변경
     *
     * @param string $crawlerName
     * @return self
     */
    public function useCrawler(string $crawlerName): self
    {
        $crawler = BookCrawlerFactory::create($crawlerName);
        $this->context->setCrawler($crawler);
        return $this;
    }

    /**
     * 현재 크롤러 반환
     *
     * @return BookCrawlerInterface
     */
    public function getCurrentCrawler(): BookCrawlerInterface
    {
        return $this->context->getCrawler();
    }

    /**
     * 키워드로 도서 검색
     *
     * @param BookSearchRequestDTO $request
     * @return BookResponseDTO
     */
    public function searchBooks(BookSearchRequestDTO $request): BookResponseDTO
    {
        return $this->context->searchBooks($request);
    }

    /**
     * 카테고리별 도서 목록 조회
     *
     * @param BookCategoryRequestDTO $request
     * @return BookResponseDTO
     */
    public function getBooksByCategory(BookCategoryRequestDTO $request): BookResponseDTO
    {
        return $this->context->getBooksByCategory($request);
    }

    /**
     * 도서 상세정보 조회
     *
     * @param BookDetailRequestDTO $request
     * @return BookResponseDTO
     */
    public function getBookDetail(BookDetailRequestDTO $request): BookResponseDTO
    {
        return $this->context->getBookDetail($request);
    }

    /**
     * 모든 카테고리의 도서 목록 조회
     *
     * @param AllCategoryRequestDTO $request
     * @return BookResponseDTO
     */
    public function getAllBooksByCategory(AllCategoryRequestDTO $request): BookResponseDTO
    {
        return $this->context->getAllBooksByCategory($request);
    }

    /**
     * 모든 크롤러로부터 도서 검색 (통합 검색)
     *
     * @param BookSearchRequestDTO $request
     * @return array<string, BookResponseDTO>
     */
    public function searchBooksFromAllCrawlers(BookSearchRequestDTO $request): array
    {
        $results = [];
        
        foreach ($this->crawlers as $crawlerName => $crawler) {
            $this->context->setCrawler($crawler);
            $result = $this->context->searchBooks($request);
            $results[$crawlerName] = $result;
        }

        return $results;
    }

    /**
     * 모든 크롤러로부터 카테고리별 도서 조회 (통합 조회)
     *
     * @param BookCategoryRequestDTO $request
     * @return array<string, BookResponseDTO>
     */
    public function getBooksByCategoryFromAllCrawlers(BookCategoryRequestDTO $request): array
    {
        $results = [];
        
        foreach ($this->crawlers as $crawlerName => $crawler) {
            $this->context->setCrawler($crawler);
            $result = $this->context->getBooksByCategory($request);
            $results[$crawlerName] = $result;
        }

        return $results;
    }

    /**
     * 사용 가능한 크롤러 목록 반환
     *
     * @return array
     */
    public function getAvailableCrawlers(): array
    {
        return BookCrawlerFactory::getAvailableCrawlers();
    }

    /**
     * 크롤러 정보 반환
     *
     * @return array
     */
    public function getCrawlerInfo(): array
    {
        return $this->context->getCrawlerInfo();
    }
}