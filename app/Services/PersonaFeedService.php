<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Book;
use App\Models\Post;
use App\Enums\PostTypeEnum;
use App\Enums\PostStatusEnum;
use Illuminate\Support\Collection;

class PersonaFeedService
{
    public function generateFeedForPersona(Persona $persona): Post
    {
        // 1. 페르소나 취향에 맞는 독서 리스트 조회
        $recommendedBooks = $this->getRecommendedBooks($persona);
        
        // 2. 리스트 중 1개 선택
        $selectedBook = $this->selectBookFromList($recommendedBooks);
        
        // 3. GPT를 통한 원고 생성
        $content = $this->generateContentWithGPT($persona, $selectedBook);
        
        // 4. Posts 테이블에 피드 생성
        return $this->createFeedPost($persona, $selectedBook, $content);
    }

    protected function getRecommendedBooks(Persona $persona): Collection
    {
        $preferences = $persona->reading_preferences;
        
        // 페르소나의 독서 취향에 따른 도서 조회 로직
        // TODO: 실제 추천 알고리즘 구현
        return Book::query()
            ->when(isset($preferences['genres']), function ($query) use ($preferences) {
                // 장르 기반 필터링
            })
            ->when(isset($preferences['authors']), function ($query) use ($preferences) {
                // 작가 기반 필터링
            })
            ->when(isset($preferences['keywords']), function ($query) use ($preferences) {
                // 키워드 기반 필터링
            })
            ->limit(20)
            ->get();
    }

    protected function selectBookFromList(Collection $books): Book
    {
        // 추천 리스트에서 랜덤하게 1개 선택
        // TODO: 더 정교한 선택 로직 구현 (가중치, 최근 피드 중복 방지 등)
        return $books->random();
    }

    protected function generateContentWithGPT(Persona $persona, Book $book): string
    {
        // GPT API를 통한 원고 생성
        // TODO: OpenAI API 연동
        
        $prompt = $this->buildPrompt($persona, $book);
        
        // 임시 더미 컨텐츠
        return "안녕하세요, {$persona->name}입니다. 오늘은 '{$book->title}'이라는 책을 소개해드리려고 합니다...";
    }

    protected function buildPrompt(Persona $persona, Book $book): string
    {
        return sprintf(
            "당신은 %s살 %s %s입니다. 취향: %s. '%s' 책에 대한 개인적인 리뷰와 추천 글을 작성해주세요.",
            $persona->age,
            $persona->gender === 'male' ? '남성' : ($persona->gender === 'female' ? '여성' : ''),
            $persona->occupation,
            json_encode($persona->reading_preferences, JSON_UNESCAPED_UNICODE),
            $book->title
        );
    }

    protected function createFeedPost(Persona $persona, Book $book, string $content): Post
    {
        return Post::create([
            'type' => PostTypeEnum::POST,
            'user_id' => null, // 페르소나가 생성한 포스트이므로 실제 사용자 ID는 null
            'title' => "📚 {$persona->name}의 도서 추천: {$book->title}",
            'content' => $content,
            'status' => PostStatusEnum::PUBLISHED,
            'meta' => [
                'persona_id' => $persona->id,
                'book_id' => $book->id,
                'generated_by' => 'ai',
                'generated_at' => now()
            ]
        ]);
    }

    public function generateFeedsForAllActivePersonas(): Collection
    {
        $personas = Persona::where('is_active', true)->get();
        $generatedFeeds = collect();

        foreach ($personas as $persona) {
            try {
                $feed = $this->generateFeedForPersona($persona);
                $generatedFeeds->push($feed);
            } catch (\Exception $e) {
                // 로그 기록
                logger()->error("페르소나 피드 생성 실패", [
                    'persona_id' => $persona->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $generatedFeeds;
    }

    public function schedulePersonaFeeds(): void
    {
        // 스케줄링을 위한 메소드
        // TODO: Laravel Task Scheduling과 연동
    }
}