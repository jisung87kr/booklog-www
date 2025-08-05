<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Persona;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserWithPersonaSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => '김민지',
                'username' => 'bookworm_minji',
                'email' => 'minji@booklog.co.kr',
                'password' => Hash::make('password'),
                'introduction' => '책을 사랑하는 도서관 사서입니다.',
                'persona_id' => 1, // 독서광 민지
            ],
            [
                'name' => '이준호',
                'username' => 'marketing_pro',
                'email' => 'junho@booklog.co.kr',
                'password' => Hash::make('password'),
                'introduction' => '마케팅 분야에서 일하고 있습니다.',
                'persona_id' => 2, // 비즈니스맨 준호
            ],
            [
                'name' => '박수빈',
                'username' => 'romance_lover',
                'email' => 'subin@booklog.co.kr',
                'password' => Hash::make('password'),
                'introduction' => '로맨스 소설을 좋아하는 대학생이에요~',
                'persona_id' => 3, // 로맨스 러버 수빈
            ],
            [
                'name' => '최현우',
                'username' => 'science_geek',
                'email' => 'hyunwoo@booklog.co.kr',
                'password' => Hash::make('password'),
                'introduction' => '과학과 기술에 관심이 많은 개발자입니다.',
                'persona_id' => 4, // 과학 덕후 현우
            ],
            [
                'name' => '장은영',
                'username' => 'mom_reader',
                'email' => 'eunyoung@booklog.co.kr',
                'password' => Hash::make('password'),
                'introduction' => '두 아이의 엄마이자 독서모임 리더입니다.',
                'persona_id' => 5, // 엄마 독서모임 은영
            ],
            [
                'name' => '김도현',
                'username' => 'philosophy_mind',
                'email' => 'dohyun@booklog.co.kr',
                'password' => Hash::make('password'),
                'introduction' => '철학과 인문학을 좋아합니다.',
                'persona_id' => 1, // 독서광 민지 스타일
            ],
            [
                'name' => '이서연',
                'username' => 'hr_leader',
                'email' => 'seoyeon@booklog.co.kr',
                'password' => Hash::make('password'),
                'introduction' => 'HR 팀장으로 일하며 자기계발서를 즐겨 읽습니다.',
                'persona_id' => 2, // 비즈니스맨 준호 스타일
            ],
            [
                'name' => '정유진',
                'username' => 'story_dreamer',
                'email' => 'yujin@booklog.co.kr',
                'password' => Hash::make('password'),
                'introduction' => '감성적인 이야기를 좋아하는 직장인입니다💕',
                'persona_id' => 3, // 로맨스 러버 수빈 스타일
            ],
            [
                'name' => '강민수',
                'username' => 'data_scientist',
                'email' => 'minsu@booklog.co.kr',
                'password' => Hash::make('password'),
                'introduction' => '데이터 사이언티스트로 일하고 있습니다.',
                'persona_id' => 4, // 과학 덕후 현우 스타일
            ],
            [
                'name' => '윤소희',
                'username' => 'family_time',
                'email' => 'sohee@booklog.co.kr',
                'password' => Hash::make('password'),
                'introduction' => '가족과 함께하는 시간을 소중히 여기는 엄마입니다.',
                'persona_id' => 5, // 엄마 독서모임 은영 스타일
            ]
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

        // 생성된 유저 정보 출력
        $this->command->info('생성된 유저 목록:');
        foreach ($users as $user) {
            $persona = Persona::find($user['persona_id']);
            $this->command->info("- {$user['name']} (@{$user['username']}) - 페르소나: {$persona->name}");
        }
    }
}
