<?php
namespace App\Services\Crawler;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AladinService{
    private int|float $ttl = 60 * 60 * 24;
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

    public function itemList($categoryId, $start=1, $queryType='ItemNewAll', $maxResults=50, $searchTarget='Book', $cover='Big', $output='js', $version='20131101'){
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
}
