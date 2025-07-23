<?php

namespace App\Services\Crawler\Contracts;

use App\Services\Crawler\DTOs\BookSearchRequestDTO;
use App\Services\Crawler\DTOs\BookCategoryRequestDTO;
use App\Services\Crawler\DTOs\BookDetailRequestDTO;
use App\Services\Crawler\DTOs\AllCategoryRequestDTO;
use App\Services\Crawler\DTOs\BookResponseDTO;

interface BookCrawlerInterface
{
    /**
     * 키워드로 도서 검색
     *
     * @param BookSearchRequestDTO $request
     * @return BookResponseDTO
     */
    public function searchBooks(BookSearchRequestDTO $request): BookResponseDTO;

    /**
     * 카테고리별 도서 목록 조회
     *
     * @param BookCategoryRequestDTO $request
     * @return BookResponseDTO
     */
    public function getBooksByCategory(BookCategoryRequestDTO $request): BookResponseDTO;

    /**
     * 도서 상세정보 조회
     *
     * @param BookDetailRequestDTO $request
     * @return BookResponseDTO
     */
    public function getBookDetail(BookDetailRequestDTO $request): BookResponseDTO;

    /**
     * 모든 카테고리의 도서 목록 조회
     *
     * @param AllCategoryRequestDTO $request
     * @return BookResponseDTO
     */
    public function getAllBooksByCategory(AllCategoryRequestDTO $request): BookResponseDTO;

    /**
     * 크롤러의 최대 결과 개수 반환
     *
     * @return int
     */
    public function getMaxResults(): int;

    /**
     * 크롤러의 최대 전체 결과 개수 반환
     *
     * @return int
     */
    public function getMaxTotalResults(): int;

    /**
     * 크롤러의 이름 반환
     *
     * @return string
     */
    public function getName(): string;
}