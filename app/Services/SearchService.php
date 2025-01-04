<?php
namespace App\Services;

use App\Enums\PostTypeEnum;
use App\Models\Post;
use App\Models\User;

class SearchService
{
    public function search($q=null) : array
    {
        if($q === null){
            $result = [
                'relatedKeywords' => [],
                'users' => User::filter(['q' => $q])->inRandomOrder()->limit(9)->get(),
            ];
        } else {
            $relatedKeywords = $this->relatedKeywords($q);
            $mergedKeywords = [];
            $mergedKeywords[] = $relatedKeywords['keyword'];
            array_map(function($keyword) use ($relatedKeywords, &$mergedKeywords){
                $mergedKeywords[] = "{$relatedKeywords['keyword']} {$keyword}";
            }, array_keys($relatedKeywords['related_keywords']));

            $result = [
                'relatedKeywords' => $mergedKeywords,
                'users' => User::filter(['q' => $q])->limit(9)->get(),
            ];
        }

        return $result;
    }

    public function relatedKeywords($keyword) : array
    {
        // FULLTEXT 검색 수행
        $results = Post::whereRaw("MATCH(content) AGAINST(?)", [$keyword])->get();

        // 단어 추출 및 빈도 계산
        $relatedTerms = [];
        foreach ($results as $result) {
            // 키워드와 연관된 단어 추출 (키워드와 가까운 단어 분석)
            preg_match_all('/\b' . preg_quote($keyword, '/') . '\s+\w+\b/', $result->content, $matches);

            foreach ($matches[0] as $match) {
                $relatedTerms[] = trim(str_replace($keyword, '', $match));
            }
        }

        // 빈도 계산
        $frequency = array_count_values($relatedTerms);
        arsort($frequency); // 빈도 높은 순으로 정렬

        return [
            'keyword' => $keyword,
            'related_keywords' => $frequency,
        ];
    }
}
