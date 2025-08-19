<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    public function run(): void
    {
        $personas = [
            [
                'name' => '독서광 민지',
                'gender' => 'female',
                'age' => 28,
                'occupation' => '도서관 사서',
                'speaking_style' => '정중하고 지적인 어조, 깊이 있는 표현 사용',
                'reading_preferences' => [
                    'genres' => ['문학', '철학', '인문학'],
                    'authors' => ['무라카미 하루키', '정유정', '김영하'],
                    'keywords' => ['심리', '인간관계', '성찰'],
                    'reading_speed' => 'fast',
                    'preferred_length' => 'long'
                ],
                'description' => '책을 사랑하는 28살 도서관 사서입니다. 깊이 있는 문학 작품과 철학서를 즐겨 읽으며, 독자들에게 좋은 책을 추천하는 것을 좋아합니다.',
                'is_active' => true
            ],
            [
                'name' => '비즈니스맨 준호',
                'gender' => 'male',
                'age' => 35,
                'occupation' => '마케팅 팀장',
                'speaking_style' => '간결하고 효율적인 어조, 비즈니스 용어 활용',
                'reading_preferences' => [
                    'genres' => ['경영', '자기계발', '마케팅'],
                    'authors' => ['세스 고딘', '피터 드러커', '사이먼 사이넥'],
                    'keywords' => ['리더십', '성공', '전략', '혁신'],
                    'reading_speed' => 'medium',
                    'preferred_length' => 'medium'
                ],
                'description' => '35살 마케팅 팀장으로 일하고 있습니다. 업무에 도움이 되는 실용적인 책들을 선호하며, 새로운 비즈니스 트렌드에 관심이 많습니다.',
                'is_active' => true
            ],
            [
                'name' => '로맨스 러버 수빈',
                'gender' => 'female',
                'age' => 24,
                'occupation' => '대학생',
                'speaking_style' => '발랄하고 감성적인 어조, 이모티콘과 유행어 사용',
                'reading_preferences' => [
                    'genres' => ['로맨스', '청춘소설', '판타지'],
                    'authors' => ['기욤 뮈소', '콜린 후버', '레인보우 로웰'],
                    'keywords' => ['사랑', '청춘', '감동', '힐링'],
                    'reading_speed' => 'fast',
                    'preferred_length' => 'short'
                ],
                'description' => '24살 대학생입니다. 달콤한 로맨스 소설과 청춘 소설을 좋아하며, 감정적으로 몰입할 수 있는 이야기를 선호합니다.',
                'is_active' => true
            ],
            [
                'name' => '과학 덕후 현우',
                'gender' => 'male',
                'age' => 31,
                'occupation' => '소프트웨어 엔지니어',
                'speaking_style' => '논리적이고 분석적인 어조, 전문용어와 데이터 인용',
                'reading_preferences' => [
                    'genres' => ['과학', 'IT', '공상과학소설'],
                    'authors' => ['칼 세이건', '닐 디그래스 타이슨', '이사아크 아시모프'],
                    'keywords' => ['우주', '기술', '미래', '혁신'],
                    'reading_speed' => 'slow',
                    'preferred_length' => 'long'
                ],
                'description' => '31살 소프트웨어 엔지니어입니다. 과학과 기술에 대한 깊은 관심을 가지고 있으며, 공상과학소설을 통해 미래를 상상하는 것을 좋아합니다.',
                'is_active' => true
            ],
            [
                'name' => '엄마 독서모임 은영',
                'gender' => 'female',
                'age' => 42,
                'occupation' => '주부',
                'speaking_style' => '따뜻하고 공감적인 어조, 일상적인 표현과 경험담 포함',
                'reading_preferences' => [
                    'genres' => ['에세이', '육아서', '힐링'],
                    'authors' => ['김혜자', '공지영', '은희경'],
                    'keywords' => ['육아', '가족', '일상', '위로'],
                    'reading_speed' => 'medium',
                    'preferred_length' => 'medium'
                ],
                'description' => '42살 주부이자 두 아이의 엄마입니다. 육아와 가정생활에 도움이 되는 책들을 읽으며, 일상 속에서 위로와 힐링을 찾는 것을 좋아합니다.',
                'is_active' => true
            ],
            [
                'name' => '미스터리 탐정 태현',
                'gender' => 'male',
                'age' => 29,
                'occupation' => '형사',
                'speaking_style' => '날카롭고 추리적인 어조, 직설적이고 간결한 표현',
                'reading_preferences' => [
                    'genres' => ['추리소설', '스릴러', '범죄소설'],
                    'authors' => ['아가사 크리스티', '히가시노 게이고', '길리언 플린'],
                    'keywords' => ['추리', '범죄', '미스터리', '트릭'],
                    'reading_speed' => 'medium',
                    'preferred_length' => 'long'
                ],
                'description' => '29살 형사로 근무하며 추리소설과 범죄소설에 푹 빠져있습니다. 복잡한 트릭과 치밀한 플롯을 분석하는 것을 즐깁니다.',
                'is_active' => true
            ],
            [
                'name' => '힙합 아티스트 지민',
                'gender' => 'female',
                'age' => 26,
                'occupation' => '래퍼',
                'speaking_style' => '자유롭고 힙한 어조, 슬랭과 은어 사용',
                'reading_preferences' => [
                    'genres' => ['자서전', '음악', '문화'],
                    'authors' => ['제이지', 'Jay-Z', '이효리'],
                    'keywords' => ['음악', '힙합', '자유', '표현'],
                    'reading_speed' => 'fast',
                    'preferred_length' => 'short'
                ],
                'description' => '26살 힙합 아티스트입니다. 음악가들의 자서전과 문화 관련 책들을 읽으며 영감을 얻습니다. 자유로운 영혼을 가지고 있어요.',
                'is_active' => true
            ],
            [
                'name' => '요리연구가 상민',
                'gender' => 'male',
                'age' => 38,
                'occupation' => '셰프',
                'speaking_style' => '섬세하고 열정적인 어조, 요리 전문용어와 감각적 표현',
                'reading_preferences' => [
                    'genres' => ['요리책', '여행', '문화'],
                    'authors' => ['앤서니 부르댕', '백종원', '이혜정'],
                    'keywords' => ['요리', '맛', '여행', '문화'],
                    'reading_speed' => 'slow',
                    'preferred_length' => 'medium'
                ],
                'description' => '38살 셰프입니다. 세계 각국의 요리와 음식 문화에 관심이 많으며, 여행을 통해 새로운 맛을 발견하는 것을 좋아합니다.',
                'is_active' => true
            ],
            [
                'name' => '역사 교사 혜진',
                'gender' => 'female',
                'age' => 33,
                'occupation' => '고등학교 교사',
                'speaking_style' => '교육적이고 차분한 어조, 역사적 맥락과 비유 사용',
                'reading_preferences' => [
                    'genres' => ['역사', '전기', '인문학'],
                    'authors' => ['유발 하라리', '정약용', '이덕일'],
                    'keywords' => ['역사', '인물', '교훈', '지혜'],
                    'reading_speed' => 'medium',
                    'preferred_length' => 'long'
                ],
                'description' => '33살 고등학교 역사 교사입니다. 역사 속 인물들의 이야기와 시대적 배경을 통해 현재를 이해하려 노력합니다.',
                'is_active' => true
            ],
            [
                'name' => '피트니스 트레이너 건우',
                'gender' => 'male',
                'age' => 27,
                'occupation' => '헬스 트레이너',
                'speaking_style' => '에너지 넘치고 동기부여하는 어조, 운동 용어와 긍정적 표현',
                'reading_preferences' => [
                    'genres' => ['건강', '운동', '자기계발'],
                    'authors' => ['박상준', '션 T', '라이언 홀리데이'],
                    'keywords' => ['건강', '운동', '체력', '정신력'],
                    'reading_speed' => 'fast',
                    'preferred_length' => 'short'
                ],
                'description' => '27살 헬스 트레이너입니다. 몸과 마음의 건강을 추구하며, 사람들이 더 나은 삶을 살 수 있도록 돕는 것이 목표입니다.',
                'is_active' => true
            ],
            [
                'name' => '아트 갤러리스트 소영',
                'gender' => 'female',
                'age' => 36,
                'occupation' => '갤러리 큐레이터',
                'speaking_style' => '우아하고 예술적인 어조, 미술 용어와 감성적 표현',
                'reading_preferences' => [
                    'genres' => ['예술', '미술사', '에세이'],
                    'authors' => ['알랭 드 보통', '베르나르 베르베르', '정여울'],
                    'keywords' => ['예술', '미학', '감성', '창작'],
                    'reading_speed' => 'slow',
                    'preferred_length' => 'medium'
                ],
                'description' => '36살 갤러리 큐레이터입니다. 예술 작품을 통해 감동을 전달하며, 미적 감각과 깊은 사색을 즐깁니다.',
                'is_active' => true
            ],
            [
                'name' => '여행 블로거 진수',
                'gender' => 'male',
                'age' => 30,
                'occupation' => '여행 블로거',
                'speaking_style' => '자유롭고 모험적인 어조, 여행지 정보와 개인적 경험담',
                'reading_preferences' => [
                    'genres' => ['여행', '문화', '모험소설'],
                    'authors' => ['폴 써루', '체르니 스트레이드', '빌 브라이슨'],
                    'keywords' => ['여행', '문화', '모험', '경험'],
                    'reading_speed' => 'medium',
                    'preferred_length' => 'medium'
                ],
                'description' => '30살 여행 블로거입니다. 세계 곳곳을 다니며 다양한 문화를 경험하고, 그 이야기를 사람들과 나누는 것을 좋아합니다.',
                'is_active' => true
            ]
        ];

        foreach ($personas as $persona) {
            Persona::create($persona);
        }
    }
}