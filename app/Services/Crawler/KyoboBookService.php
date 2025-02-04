<?php
namespace App\Services\Crawler;

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

class KyoboBookService{
    private $size = 20;
    public function __construct()
    {

    }

    public function getMaxPage($totalCount, $size)
    {
        return ceil($totalCount / $size);
    }

    public function getAllBooksByCategory($category)
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

    public function searchBook($keyword, $len=12, )
    {

    }

    public function getBookDetail()
    {

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
