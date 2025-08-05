<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Book;
use App\Models\Post;
use App\Models\User;
use App\Enums\PostTypeEnum;
use App\Enums\PostStatusEnum;
use App\Services\OpenAi\OpenAiService;
use Illuminate\Support\Collection;

class PersonaFeedService
{
    protected $openAiService;
    public function __construct(OpenAiService $openAiService)
    {
        $this->openAiService = $openAiService;
    }

    public function generateFeedForPersona(User $user): Post
    {
        $persona = $user->persona;
        $content = $this->generateContentWithGPT($persona);
        return $this->createFeedPost($persona, $content);
    }

    protected function generateContentWithGPT(Persona $persona)
    {
        // 페르소나 정보 포맷팅
        $preferences = $persona->reading_preferences;
        $genresText = implode(', ', $preferences['genres'] ?? []);
        $authorsText = implode(', ', $preferences['authors'] ?? []);
        $keywordsText = implode(', ', $preferences['keywords'] ?? []);
        $speakingStyle = $persona->speaking_style ?? '자연스럽고 친근한';

        $systemPrompt = "당신은 '{$persona->name}' 역할을 합니다.

프로필:
- 나이: {$persona->age}세
- 성별: {$persona->gender}
- 직업: {$persona->occupation}
- 성격: {$persona->description}
- 말투/어조: {$speakingStyle}

독서 취향:
- 선호 장르: {$genresText}
- 좋아하는 작가: {$authorsText}
- 관심 키워드: {$keywordsText}

이 페르소나의 관점에서 반드시 '{$speakingStyle}' 말투를 사용하여 자연스럽고 개성 있는 도서 추천 글이나 감상을 작성해주세요.";

        $userPrompt = "다음 조건에 맞는 도서 추천 글이나 감상을 작성해주세요:

1. 길이: 150-200자 내외(줄내림 적당히 사용)
2. 톤: 반드시 '{$speakingStyle}' 말투를 사용하여 페르소나의 개성을 살려주세요
3. 내용: 구체적인 책 제목을 포함하여 추천하거나, 최근 읽은 책에 대한 감상
4. 스타일: SNS 게시글처럼 친근하고 생동감 있게
5. 해시태그: 관련 해시태그 2-3개 포함

**중요**:
책제목과 저자를 틀려서는 안됩니다. 반드시 정확한 책 제목과 저자를 사용하세요.

**응답 형식 (중요)**:
- 반드시 유효한 JSON 형식으로만 응답하세요
- 다른 텍스트는 포함하지 마세요
- 마크다운 코드 블록(```)은 사용하지 마세요
- 반드시 큰따옴표(\")를 사용하세요, 작은따옴표(') 사용금지

{
    \"title\": \"피드 제목\",
    \"book_title\": \"책 제목\",
    \"content\": \"추천 글 내용\",
    \"hashtags\": \"#해시태그1 #해시태그2 #해시태그3\"
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
                // 여전히 실패하면 더미 데이터 반환
                $data = [
                    'title' => "📚 {$persona->name}의 도서 추천",
                    'content' => "오늘은 제가 좋아하는 {$genresText} 장르의 책을 추천해드리려고 합니다. [AI 응답 파싱 실패로 인한 더미 컨텐츠]",
                    'hashtags' => '#독서 #책추천 #' . str_replace(' ', '', $persona->name)
                ];

                logger()->warning('AI 응답 JSON 파싱 실패', [
                    'persona_id' => $persona->id,
                    'raw_response' => $result,
                    'json_error' => json_last_error_msg()
                ]);
            }
        }

        return $data;

    }

    protected function createFeedPost(Persona $persona, array $content): Post
    {
        // 페르소나의 랜덤한 유저 ID를 사용하여 포스트 생성
        $user = $persona->users()->inRandomOrder()->first();
        return Post::create([
            'type' => PostTypeEnum::POST,
            'user_id' => $user->id, // 페르소나가 생성한 포스트이므로 실제 사용자 ID는 null
            'title' => "📚 {$content['title']}",
            'content' => $content['content']." ".$content['hashtags'],
            'status' => PostStatusEnum::PUBLISHED,
            'meta' => [
                'persona_id' => $persona->id,
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
