<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Post;
use App\Models\User;
use App\Enums\PostTypeEnum;
use App\Enums\PostStatusEnum;
use App\Services\OpenAi\OpenAiService;
use App\Services\Crawler\BookCrawlerService;
use App\Services\Crawler\DTOs\BookSearchRequestDTO;
use App\Services\Crawler\DTOs\BookDetailDTO;
use Illuminate\Support\Collection;
use App\Models\Book;

class PersonaFeedService
{
    protected OpenAiService $openAiService;
    protected BookCrawlerService $bookCrawlerService;

    public function __construct(
        OpenAiService $openAiService,
        BookCrawlerService $bookCrawlerService
    ) {
        $this->openAiService = $openAiService;
        $this->bookCrawlerService = $bookCrawlerService;
    }

    public function generateFeedForPersona(User $user): Post
    {
        $persona = $user->persona;
        $content = $this->generateContentWithGPT($persona);
        return $this->createFeedPost($persona, $content);
    }

    public function generateContentWithGPT(Persona $persona)
    {
        // 알라딘 API에서 도서 검색
        $book = $this->selectRandomBookForPersona($persona);

        Book::updateOrCreate(
            ['isbn' => $book->isbn],
            [
                'title' => $book->title,
                'author' => $book->author,
                'description' => $book->description,
                'product_id' => $book->productId,
                'type' => $book->type,
                'sale_price' => $book->salePrice,
                'price' => $book->price,
                'total_pages' => $book->totalPages,
                'publisher' => $book->publisher,
                'published_date' => $book->publishDate,
                'cover_image' => $book->coverImage,
                'link' => $book->link
            ]
        );

        if (!$book) {
            throw new \Exception('추천할 도서를 알라딘 API에서 찾을 수 없습니다.');
        }

        // 페르소나 정보 포맷팅
        $preferences = $persona->reading_preferences;
        $genresText = implode(', ', $preferences['genres'] ?? []);
        $keywordsText = implode(', ', $preferences['keywords'] ?? []);
        $speakingStyle = $persona->speaking_style ?? '자연스럽고 친근한';

        $systemPrompt = "당신은 SNS 피드를 생성하는 역할을 합니다.

프로필:
- 나이: {$persona->age}세
- 성별: {$persona->gender}
- 직업: {$persona->occupation}
- 성격: {$persona->description}
- 말투/어조: {$speakingStyle}

독서 취향:
- 선호 장르: {$genresText}
- 관심 키워드: {$keywordsText}

이 페르소나의 관점에서 반드시 '{$speakingStyle}' 말투를 사용하여 주어진 실제 도서에 대한 추천 글이나 감상을 작성해주세요.";

$userPrompt = "다음 **실제 존재하는 도서**에 대한 추천 글이나 감상을 작성해주세요:

**도서 정보 (절대 변경 금지)**:
- 제목: {$book->title}
- 저자: {$book->author}
- 설명: {$book->description}
- ISBN: {$book->isbn}

**작성 조건**:
1. 길이: 150-200자 내외
2. 줄내림: 자연스러운 호흡과 문장 단위로 줄내림 사용 (\\n으로 표현)
   - 예: 인사말 → 줄내림 → 책 소개 → 줄내림 → 감상/추천 이유 → 줄내림 → 해시태그
3. 톤: 반드시 '{$speakingStyle}' 말투를 사용하여 페르소나의 개성을 살려주세요
4. 내용: 위에 제공된 **정확한 책 제목과 저자명**만 사용
5. 스타일: SNS 게시글처럼 친근하고 생동감 있게
6. 해시태그: 관련 해시태그 2-3개 포함

**절대 금지**:
- 책 제목이나 저자명을 임의로 변경하거나 다른 책으로 바꾸지 마세요
- 존재하지 않는 책을 언급하지 마세요

**응답 형식 (중요)**:
- 반드시 유효한 JSON 형식으로만 응답하세요
- 다른 텍스트는 포함하지 마세요
- 마크다운 코드 블록(```)은 사용하지 마세요
- 반드시 큰따옴표(\")를 사용하세요, 작은따옴표(') 사용금지

{
    \"title\": \"피드 제목\",
    \"book_title\": \"{$book->title}\",
    \"author\": \"{$book->author}\",
    \"content\": \"피드 내용\",
    \"hashtags\": \"#독서 #책추천 #감동\"
}
";

        $message = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'user',
                'content' => $userPrompt
            ]
        ];

        $result = $this->openAiService->chat($message);

        // JSON 파싱 시도
        $data = json_decode($result, true);

        // JSON 파싱 실패 시 처리
        if (json_last_error() !== JSON_ERROR_NONE) {
            // 마크다운 코드 블록 제거 시도
            $cleanResult = preg_replace('/```json\s*/', '', $result);
            $cleanResult = preg_replace('/```\s*$/', '', $cleanResult);
            $cleanResult = trim($cleanResult);

            // 다시 파싱 시도
            $data = json_decode($cleanResult, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // 여전히 실패하면 실제 도서 정보를 사용한 더미 데이터 반환
                $data = [
                    'title' => "📚 {$persona->name}의 도서 추천",
                    'book_title' => $book->title,
                    'author' => $book->author,
                    'content' => "오늘은 정말 좋은 책을 발견했어요! 📖\n\n'{$book->title}' by {$book->author}\n\n이 책 정말 추천드려요!\n\n[AI 응답 파싱 실패로 인한 더미 컨텐츠]",
                    'hashtags' => '#독서 #책추천 #' . str_replace(' ', '', $persona->name)
                ];

                logger()->warning('AI 응답 JSON 파싱 실패', [
                    'persona_id' => $persona->id,
                    'book_isbn' => $book->isbn,
                    'raw_response' => $result,
                    'json_error' => json_last_error_msg()
                ]);
            }
        }

        return $data;
    }

    public function selectRandomBookForPersona(Persona $persona): ?BookDetailDTO
    {
        $preferences = $persona->reading_preferences;
        $authors = $preferences['authors'] ?? [];
        $keywords = $preferences['keywords'] ?? [];

        // 선호 작가가 있으면 우선 검색
        if (!empty($authors)) {
            foreach ($authors as $author) {
                $searchRequest = new BookSearchRequestDTO($author, 1, 10);
                $response = $this->bookCrawlerService->searchBooks($searchRequest);

                if ($response->success && !empty($response->data)) {
                    // 랜덤하게 선택
                    $randomIndex = array_rand($response->data);
                    return $response->data[$randomIndex];
                }
            }
        }

        // 키워드 기반 검색
        if (!empty($keywords)) {
            foreach ($keywords as $keyword) {
                $searchRequest = new BookSearchRequestDTO($keyword, 1, 10);
                $response = $this->bookCrawlerService->searchBooks($searchRequest);

                if ($response->success && !empty($response->data)) {
                    // 랜덤하게 선택
                    $randomIndex = array_rand($response->data);
                    return $response->data[$randomIndex];
                }
            }
        }

        // 아무것도 찾지 못하면 베스트셀러나 신간에서 검색
        $fallbackQueries = ['베스트셀러', '소설', '에세이', '인문'];
        foreach ($fallbackQueries as $query) {
            $searchRequest = new BookSearchRequestDTO($query, 1, 20);
            $response = $this->bookCrawlerService->searchBooks($searchRequest);

            if ($response->success && !empty($response->data)) {
                // 랜덤하게 선택
                $randomIndex = array_rand($response->data);
                return $response->data[$randomIndex];
            }
        }

        return null;
    }

    protected function createFeedPost(Persona $persona, array $content): Post
    {
        // 페르소나의 랜덤한 유저 ID를 사용하여 포스트 생성
        $user = $persona->users()->inRandomOrder()->first();
        return Post::create([
            'type' => PostTypeEnum::POST,
            'user_id' => $user->id,
            'title' => isset($content['title']) ? "📚 {$content['title']}" : "📚 {$persona->name}의 독서 피드",
            'content' => $content['content']." ".($content['hashtags'] ?? ''),
            'status' => PostStatusEnum::PUBLISHED,
            'published_at' => now(),
            'meta' => [
                'persona_id' => $persona->id,
                'generated_by' => 'ai',
                'generated_at' => now()->toISOString(),
                'book_title' => $content['book_title'] ?? null,
                'author' => $content['author'] ?? null
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
