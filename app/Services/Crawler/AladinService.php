<?php
namespace App\Services\Crawler;

use App\Services\Crawler\Contracts\BookCrawlerInterface;
use App\Services\Crawler\DTOs\BookSearchRequestDTO;
use App\Services\Crawler\DTOs\BookCategoryRequestDTO;
use App\Services\Crawler\DTOs\BookDetailRequestDTO;
use App\Services\Crawler\DTOs\AllCategoryRequestDTO;
use App\Services\Crawler\DTOs\BookResponseDTO;
use App\Utils\CommonUtil;
use App\Utils\RequestUtil;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AladinService implements BookCrawlerInterface
{
    private int|float $ttl = 60 * 60 * 24;
    private $maxTotalResults = 1000; // 전체 검색결과 1000개 까지 검색 가능
    private $maxResults = 50; // 한페이지당 50개 까지 검색 가능

    public function getMaxTotalResults(): int
    {
        return $this->maxTotalResults;
    }

    public function getMaxResults(): int
    {
        return $this->maxResults;
    }

    public function getName(): string
    {
        return 'Aladin';
    }

    public function itemSearch($query, $start=1, $queryType='Keyword', $maxResults=50, $searchTarget='book', $cover='Big', $output='js', $version='20131101')
    {
        $params = [
            'ttbkey' => config('aladin.ttb_key'),
            'Query' => $query,
            'QueryType' => $queryType,
            'MaxResults' => $maxResults,
            'start' => $start,
            'SearchTarget' => $searchTarget,
            'Cover' => $cover,
            'output' => $output,
            'Version' => $version,
        ];

        $cacheKey = 'aladinItemSearch' . $query . $queryType . $maxResults . $start . $searchTarget . $cover . $output . $version;
        $response = Cache::remember($cacheKey, $this->ttl, function() use ($params){
            $response = Http::get('http://www.aladin.co.kr/ttb/api/ItemSearch.aspx', $params);
            return $response->json();
        });

        if(!$response){
            throw new \Exception('Aladin itemSearch API Error');
        }

        return $response;
    }

    public function itemList($categoryId, $start=1, $maxResults=50, $queryType='ItemNewAll', $searchTarget='Book', $cover='Big', $output='js', $version='20131101'){
        $params = [
            'ttbkey' => config('aladin.ttb_key'),
            'CategoryId' => $categoryId,
            'QueryType' => $queryType,
            'MaxResults' => $maxResults,
            'start' => $start,
            'SearchTarget' => $searchTarget,
            'Cover' => $cover,
            'output' => $output,
            'Version' => $version,
        ];
        $cacheKey = 'aladinItemSearch' . $categoryId . $queryType . $maxResults . $start . $searchTarget . $cover . $output . $version;
        $response = Cache::remember($cacheKey, $this->ttl, function() use ($params){
            $response = Http::get('http://www.aladin.co.kr/ttb/api/ItemList.aspx', $params);
            return $response->json();
        });

        if(!$response){
            throw new \Exception('Aladin itemList API Error');
        }

        return $response;
    }

    public function itemLookUp($itemId, $itemIdType='ISBN', $optResult='ebookList,usedList,reviewList', $cover='Big', $output='js', $version='20131101'){
        $params = [
            'ttbkey' => config('aladin.ttb_key'),
            'ItemId' => $itemId,
            'ItemIdType' => $itemIdType,
            'OptResult' => $optResult,
            'Cover' => $cover,
            'output' => $output,
            'Version' => $version,
        ];
        $cacheKey = 'aladinItemLookUp' . $itemId . $itemIdType . $optResult . $cover . $output . $version;
        $response = Cache::remember($cacheKey, $this->ttl, function() use ($params){
            $response = Http::get('http://www.aladin.co.kr/ttb/api/ItemLookUp.aspx', $params);
            return $response->json();
        });

        if(!$response){
            throw new \Exception('Aladin itemLookUp API Error');
        }

        return $response;
    }

    public function itemListAll($categoryId)
    {
        $cacheKey = 'aladinItemListAll' . $categoryId;
        $result = Cache::remember($cacheKey . $categoryId, $this->ttl, function() use ($categoryId) {
            $firstPage = $this->itemList($categoryId, '1');
            $maxPage = CommonUtil::getMaxPage($firstPage['totalResults'], $firstPage['itemsPerPage']);
            $requests = [];
            for($page = 2; $page <= $maxPage; $page++){
                if($this->getMaxResults() * $page > $this->getMaxTotalResults()){
                    break;
                }

                $params = [
                    'ttbkey' => config('aladin.ttb_key'),
                    'CategoryId' => 1,
                    'QueryType' => 'ItemNewAll',
                    'MaxResults' => $this->getMaxResults(),
                    'start' => $page,
                    'SearchTarget' => 'book',
                    'Cover' => 'Big',
                    'output' => 'js',
                    'Version' => '20131101',
                ];
                $requests[] = new Request('GET', 'http://www.aladin.co.kr/ttb/api/ItemList.aspx?'.http_build_query($params));
            }
            $client = new Client();
            $requestUtil = new RequestUtil();
            return $requestUtil->sendAll($client, $requests);
        });

        return $result;
    }

    public function searchBooks(BookSearchRequestDTO $request): BookResponseDTO
    {
        try {
            $result = $this->itemSearch($request->query, $request->page, 'Keyword', $request->limit);
            $result['page'] = $request->page;
            return $this->buildBookResponseDTO($result);
        } catch (\Exception $e) {
            return BookResponseDTO::error($e->getMessage(), $this->getName());
        }
    }

    public function getBooksByCategory(BookCategoryRequestDTO $request): BookResponseDTO
    {
        try {
            $result = $this->itemList($request->categoryId, $request->page, $request->limit);
            $result['page'] = $request->page;
            return $this->buildBookResponseDTO($result);
        } catch (\Exception $e) {
            return BookResponseDTO::error($e->getMessage(), $this->getName());
        }
    }

    public function getBookDetail(BookDetailRequestDTO $request): BookResponseDTO
    {
        try {
            $result = $this->itemLookUp($request->bookId, $request->idType);
            $result['item'][0]['type'] = $this->getName();
            $result = CommonUtil::convertAladinItemToBookDTO($result['item'][0] ?? []);
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
            $result = $this->itemListAll($request->categoryId);
            $result['success'] = array_map(function ($item) {
                $item['page'] = $item['startIndex'];
                return $this->buildBookResponseDTO($item);
            }, $result['success']);
            return BookResponseDTO::success(
                data: $result,
                source: $this->getName()
            );
        } catch (\Exception $e) {
            return BookResponseDTO::error($e->getMessage(), $this->getName());
        }
    }

    public function buildBookResponseDTO($data)
    {
        $data['item'] = array_map(function($item) {
            $item['type'] = $this->getName();
            $item = CommonUtil::convertAladinItemToBookDTO($item);
            return $item;
        }, $data['item']);

        return BookResponseDTO::success(
            data: $data['item'],
            source: $this->getName(),
            totalCount: $data['totalResults'] ?? 0,
            currentPage: $data['page'],
            totalPages: CommonUtil::getMaxPage($data['totalResults'], $data['itemsPerPage'])
        );
    }
}
