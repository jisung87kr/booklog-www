<?php
namespace App\Services\Crawler;
use App\Utils\CommonUtil;
use App\Utils\RequestUtil;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AladinService{
    private int|float $ttl = 60 * 60 * 24;
    private $maxTotalResults = 1000; // 전체 검색결과 1000개 까지 검색 가능
    private $maxResults = 50; // 한페이지당 50개 까지 검색 가능

    public function getMaxTotalResult(){
        return $this->maxTotalResults;
    }

    public function getMaxResults(){
        return $this->maxResults;
    }

    public function itemSearch($query, $start=1, $queryType='Keyword', $maxResults=50, $searchTarget='book', $cover='Big', $output='js', $version='20131101')
    {
        $params = [
            'ttbkey' => env('ALADIN_TTB_KEY'),
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
        return $response;
    }

    public function itemList($categoryId, $start=1, $maxResults=50, $queryType='ItemNewAll', $searchTarget='Book', $cover='Big', $output='js', $version='20131101'){
        $params = [
            'ttbkey' => env('ALADIN_TTB_KEY'),
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
        return $response;
    }

    public function itemLookUp($itemId, $itemIdType='ISBN', $optResult='ebookList,usedList,reviewList', $cover='Big', $output='js', $version='20131101'){
        $params = [
            'ttbkey' => env('ALADIN_TTB_KEY'),
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
        return $response;
    }

    public function itemListAll($categoryId)
    {
        $firstPage = $this->itemList($categoryId, '1');
        $maxPage = CommonUtil::getMaxPage($firstPage['totalResults'], $firstPage['itemsPerPage']);
        $requests = [];
        for($page = 2; $page <= $maxPage; $page++){
            if($this->getMaxResults() * $page > $this->getMaxTotalResult()){
                break;
            }

            $params = [
                'ttbkey' => env('ALADIN_TTB_KEY'),
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
    }
}
