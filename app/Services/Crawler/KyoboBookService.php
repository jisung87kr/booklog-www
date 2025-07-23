<?php
namespace App\Services\Crawler;

use App\Services\Crawler\Contracts\BookCrawlerInterface;
use App\Services\Crawler\DTOs\BookSearchRequestDTO;
use App\Services\Crawler\DTOs\BookCategoryRequestDTO;
use App\Services\Crawler\DTOs\BookDetailRequestDTO;
use App\Services\Crawler\DTOs\AllCategoryRequestDTO;
use App\Services\Crawler\DTOs\BookResponseDTO;
use App\Utils\RequestUtil;
use GuzzleHttp\Client;
//use GuzzleHttp\Promise;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Promise;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class KyoboBookService implements BookCrawlerInterface
{
    private $size = 20;
    private $maxTotalResults = 1000;

    public function __construct()
    {

    }

    public function getName(): string
    {
        return 'Kyobo';
    }

    public function getMaxResults(): int
    {
        return $this->size;
    }

    public function getMaxTotalResults(): int
    {
        return $this->maxTotalResults;
    }

    public function getMaxPage($totalCount, $size)
    {
        return ceil($totalCount / $size);
    }

    public function getAllBooksFromCategory($category)
    {
        try {
            $firstPage = $this->getBookList(1, $this->size, $category);
            if ($firstPage['statusCode'] != 200) {
                throw new \Exception('Failed to get book list');
            }

            $totalCount = $firstPage['data']['totalCount'];
            $maxPage = $this->getMaxPage($totalCount, $this->size);

            $requestUtil = new RequestUtil();
            $headers = [
                'User-Agent' => $requestUtil->generateRandomUserAgent(),
                'timeout' => 10,
            ];

            $client = new Client(['headers' => $headers]);
            $requests = [];

            for($page = 2; $page <= $maxPage; $page++) {
                $params = [
                    'page' => $page,
                    'size' => $this->size,
                    'saleCmdtClstCode' => $category,
                    'sort' => 'new',
                    'saleCmdtDvsnCode' => 'KOR',
                ];

                $url = 'https://product.kyobobook.co.kr/api/gw/pdt/category/all?'.http_build_query($params);
                $request = new Request('GET', $url);
                $request->cacheKey = 'bookList' . $page . $this->size . $category . 'new' . 'KOR';
                $requests[$page] = $request;
            }

            $result = $requestUtil->sendChunk($client, $requests);
            $result['success'][1] = $firstPage;
            ksort($result['success']);
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getBookList($page, $size, $category, $sort='new', $division='KOR')
    {
        $cacheKey = 'bookList' . $page . $size . $category . $sort . $division;
        return Cache::remember($cacheKey, 60, function() use($page, $size, $category, $sort, $division) {
            $requestUtil = new RequestUtil();
            $headers = [
                'User-Agent' => $requestUtil->generateRandomUserAgent(),
            ];
            $client = new Client(['headers' => $headers]);
            $options = [
                'query' => [
                    'page' => $page,
                    'size' => $size,
                    'saleCmdtClstCode' => $category,
                    'sort' => $sort,
                    'saleCmdtDvsnCode' => $division,
                ]];

            $response = $client->get('https://product.kyobobook.co.kr/api/gw/pdt/category/all', $options);
            return json_decode($response->getBody()->getContents(), true);
        });
    }

    public function getCategory()
    {
        return [
            'category1',
            'category2',
            'category3',
            'category4',
            'category5',
        ];
    }

    public function getNewArrival()
    {
        return [
            'book1',
            'book2',
            'book3',
            'book4',
            'book5',
        ];
    }

    public function searchBooks(BookSearchRequestDTO $request): BookResponseDTO
    {
        try {
            $result = $this->searchBook($request->query, $request->limit);
            return BookResponseDTO::success(
                data: $result,
                source: $this->getName(),
                currentPage: $request->page
            );
        } catch (\Exception $e) {
            return BookResponseDTO::error($e->getMessage(), $this->getName());
        }
    }

    public function getBooksByCategory(BookCategoryRequestDTO $request): BookResponseDTO
    {
        try {
            $result = $this->getBookList($request->page, $request->limit, $request->categoryId);
            return BookResponseDTO::success(
                data: $result,
                source: $this->getName(),
                totalCount: $result['data']['totalCount'] ?? 0,
                currentPage: $request->page
            );
        } catch (\Exception $e) {
            return BookResponseDTO::error($e->getMessage(), $this->getName());
        }
    }

    public function getBookDetail(BookDetailRequestDTO $request): BookResponseDTO
    {
        try {
            $result = $this->getBookDetailById($request->bookId);
            return BookResponseDTO::success(
                data: $result,
                source: $this->getName()
            );
        } catch (\Exception $e) {
            return BookResponseDTO::error($e->getMessage(), $this->getName());
        }
    }

    public function getAllBooksByCategory(AllCategoryRequestDTO $request): BookResponseDTO
    {
        try {
            $result = $this->getAllBooksFromCategory($request->categoryId);
            return BookResponseDTO::success(
                data: $result,
                source: $this->getName()
            );
        } catch (\Exception $e) {
            return BookResponseDTO::error($e->getMessage(), $this->getName());
        }
    }

    public function searchBook($keyword, $len=12)
    {
        // TODO: Implement search functionality
        return [];
    }

    public function getBookDetailById($bookId)
    {
        // TODO: Implement book detail functionality
        return [];
    }

    public function getBookReview()
    {

    }

    public function getBookStatistics()
    {

    }

    public function searchAuthor()
    {

    }

    public function getAuthorDetail()
    {

    }

    public function getAuthorList()
    {
        return [
            'author1',
            'author2',
            'author3',
            'author4',
            'author5',
        ];
    }
}
