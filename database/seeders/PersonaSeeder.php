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
                'reading_preferences' => [
                    'genres' => ['에세이', '육아서', '힐링'],
                    'authors' => ['김혜자', '공지영', '은희경'],
                    'keywords' => ['육아', '가족', '일상', '위로'],
                    'reading_speed' => 'medium',
                    'preferred_length' => 'medium'
                ],
                'description' => '42살 주부이자 두 아이의 엄마입니다. 육아와 가정생활에 도움이 되는 책들을 읽으며, 일상 속에서 위로와 힐링을 찾는 것을 좋아합니다.',
                'is_active' => true
            ]
        ];

        foreach ($personas as $persona) {
            Persona::create($persona);
        }
    }
}