<?php
namespace App\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Promise\Utils;

class RequestUtil{
    private mixed $retryCount;
    private mixed $retryDelay;
    private mixed $chunkSize;
    public function __construct($retryCount=3, $retryDelay=1000, $chunkSize=20)
    {
        $this->retryCount = $retryCount;
        $this->retryDelay = $retryDelay;
        $this->chunkSize = $chunkSize;
    }


    public function generateStack(): HandlerStack
    {
        // Retry 미들웨어 설정
        $retryMiddleware = Middleware::retry(
            function ($retries, $request, $response, $exception) {
                return $retries < $this->retryCount && ($exception instanceof RequestException); // 최대 3번 재시도
            },
            function ($retries) {
                return $this->retryDelay; // 1초 대기 후 재시도
            }
        );

        // 클라이언트 생성 시 미들웨어 포함
        $stack = HandlerStack::create();
        $stack->push($retryMiddleware);

        return $stack;
    }

    /**
     * @param Client $client
     * @param array $requests
     * @return array|array[]
     */
    public function sendAll(Client $client, array $requests): array
    {
        $promises = [];
        $result = [
            'success' => [],
            'fail' => [],
        ];

        foreach ($requests as $index => $request) {
            if(isset($request->cacheKey)) {
                $cachedResponse = Cache::get($request->cacheKey);
                if($cachedResponse) {
                    $result['success'][$index] = json_decode($cachedResponse, true);
                    continue;
                }
            }
            $promises[$index] = $client->sendAsync($requests[$index]);
        }

        $responses = Utils::settle($promises)->wait();
        ksort($responses);

        foreach ($responses as $index => $response) {
            if($response['state'] == 'fulfilled') {
                $result['success'][$index] = json_decode($response['value']->getBody()->getContents(), true);
                if(isset($requests[$index]->cacheKey)){
                    Cache::put($requests[$index]->cacheKey, json_encode($result['success'][$index]), 60 * 60 * 24);
                }
            } else {
                $result['fail'][$index] = $response;
            }
        }

        return $result;
    }

    public function sendChunk(Client $client, array $requests): array
    {
        $promises = [];
        $result = [
            'success' => [],
            'fail' => [],
        ];

        $pageNumbers = range(array_key_first($requests), array_key_last($requests));

        collect($pageNumbers)->chunk($this->chunkSize)->each(function($chunk) use ($client, $requests, &$promises, &$result) {
            foreach ($chunk as $index) {
                $request = $requests[$index];
                if(isset($request->cacheKey)) {
                    $cachedResponse = Cache::get($request->cacheKey);
                    if($cachedResponse) {
                        $result['success'][$index] = json_decode($cachedResponse, true);
                        continue;
                    }
                }
                $promises[$index] = $client->sendAsync($request);
            }

            $responses = Utils::settle($promises)->wait();
            ksort($responses); // 응답을 정렬 (페이지 번호 순으로)

            foreach ($responses as $index => $response) {
                if ($response['state'] == 'fulfilled') {
                    $result['success'][$index] = json_decode($response['value']->getBody()->getContents(), true);
                    if(isset($requests[$index]->cacheKey)){
                        Cache::put($requests[$index]->cacheKey, json_encode($result['success'][$index]), 60 * 60 * 24);
                    }
                } else {
                    $result['fail'][$index] = $response;
                }
            }

            // 청크 처리가 끝난 후 promises 배열 초기화 (다음 청크를 위해)
            $promises = [];
        });

        return $result;
    }

    public function generateRandomUserAgent() {
        // 브라우저 종류
        $browsers = [
            'Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'MSIE', 'Trident'
        ];

        // 운영체제 종류
        $osList = [
            'Windows NT 10.0', 'Windows NT 6.1', 'Macintosh; Intel Mac OS X 10_15_0', 'X11; Ubuntu; Linux x86_64', 'iPhone; CPU iPhone OS 13_3 like Mac OS X', 'Android 9'
        ];

        // 디바이스
        $devices = [
            'Mobile', 'Tablet', 'Desktop'
        ];

        // 랜덤하게 각 요소 선택
        $browser = $browsers[array_rand($browsers)];
        $os = $osList[array_rand($osList)];
        $device = $devices[array_rand($devices)];

        // 각 요소에 따라 User-Agent 형식 만들기
        if ($device == 'Mobile' || $device == 'Tablet') {
            return "$browser/89.0 (KHTML, like Gecko) $os; $device)";
        } else {
            return "$browser/89.0 (Windows NT 10.0; Win64; x64) $os; $device)";
        }
    }
}
