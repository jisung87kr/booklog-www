# BookLog - 독서 로그 웹 애플리케이션

Laravel 기반의 독서 기록 및 커뮤니티 플랫폼입니다.

## 기술 스택

### 백엔드
- **Framework**: Laravel 10.x
- **PHP**: ^8.1
- **Database**: MariaDB
- **Authentication**: Laravel Jetstream + Sanctum
- **Authorization**: Spatie Laravel Permission

### 프론트엔드
- **Framework**: Vue.js 3
- **Build Tool**: Vite
- **CSS Framework**: TailwindCSS
- **UI Components**: Flowbite
- **State Management**: Pinia

### 배포 & 인프라
- **Containerization**: Docker + Docker Compose
- **Web Server**: Apache
- **Development**: Laravel Sail

### 주요 라이브러리
- **AI Integration**: OpenAI (orhanerday/open-ai)
- **Real-time**: Livewire 3
- **File Upload**: 커스텀 이미지/첨부파일 서비스
- **Development**: Clockwork (디버깅)

## 주요 기능

### 📚 도서 관리
- 알라딘 API를 통한 도서 검색 및 자동 등록
- 도서 정보 관리 (ISBN, 표지, 출판정보)
- 카테고리 및 작가 관리

### 🗂️ 서재 시스템
- 사용자별 다중 서재 생성
- 서재별 도서 분류 및 순서 관리
- 공개/비공개 서재 설정

### 📝 게시글 & 커뮤니티
- 독서 리뷰 및 감상 게시글
- 댓글 및 대댓글 시스템
- 사용자 간 멘션 기능

### 👥 소셜 기능
- 사용자 팔로우/언팔로우
- 활동 피드 (팔로워 활동 추적)
- 사용자 액션 기록 시스템

### 🤖 AI 페르소나 시스템
- OpenAI 기반 개인화된 피드 생성
- 페르소나별 맞춤 콘텐츠 추천
- 다양한 성격과 취향의 AI 페르소나

### 🏆 배지 & 성취 시스템
- 사용자 활동 기반 배지 획득
- 독서 성취도 추적

## 데이터베이스 구조

### 핵심 테이블
- `users` - 사용자 정보
- `books` - 도서 정보
- `posts` - 게시글
- `user_bookcases` - 사용자 서재
- `book_user_bookcase` - 서재-도서 연결
- `personas` - AI 페르소나

### 관계형 테이블
- `follows` - 팔로우 관계
- `comments` - 댓글 (다형성)
- `user_actions` - 사용자 활동 (다형성)
- `tags`, `taggables` - 태그 시스템
- `images`, `attachments` - 파일 관리

## 설치 및 실행

### 환경 요구사항
- Docker & Docker Compose
- PHP 8.1+
- Node.js & npm

### Docker 환경 실행
```bash
# 컨테이너 실행
cd docker
docker-compose up -d

# 웹 컨테이너 접속
docker exec -it booklog_web bash

# Laravel 명령어 실행
docker exec booklog_web php artisan migrate
docker exec booklog_web php artisan db:seed
```

### 개발 환경 설정
```bash
# 의존성 설치
composer install
npm install

# 환경 설정
cp .env.example .env
php artisan key:generate

# 데이터베이스 설정
php artisan migrate
php artisan db:seed

# 프론트엔드 빌드
npm run dev
```

## API 엔드포인트

### 인증이 필요한 API
- `POST /api/books` - 도서 등록
- `GET /api/users/{user}/bookcases` - 사용자 서재 목록
- `POST /api/posts` - 게시글 작성
- `POST /api/follows` - 팔로우
- `POST /api/personas/{persona}/generate-feed` - AI 피드 생성

### 공개 API
- `GET /api/feeds` - 피드 조회
- `GET /api/search` - 통합 검색
- `GET /api/posts` - 게시글 목록
- `GET /api/@{user}` - 사용자 프로필

## 주요 컨트롤러

### Web Routes
- `BookController` - 도서 관리
- `UserController` - 사용자 프로필
- `SearchController` - 검색 기능
- `ActivityController` - 활동 피드
- `Admin\AdminController` - 관리자 기능

### API Controllers
- `BookApiController` - 도서 API
- `PostApiController` - 게시글 API
- `UserBookcaseApiController` - 서재 API
- `PersonaController` - AI 페르소나응
- API

## 개발 명령어

```bash
# PHP Artisan 명령어 (Docker 환경)
docker exec booklog_web php artisan migrate
docker exec booklog_web php artisan db:seed
docker exec booklog_web php artisan queue:work
docker exec booklog_web php artisan schedule:run

# 프론트엔드 개발
npm run dev      # 개발 서버 실행
npm run build    # 프로덕션 빌드

# 테스트
php artisan test
```

## 프로젝트 구조

```
www/
├── app/
│   ├── Http/Controllers/    # 컨트롤러
│   ├── Models/             # Eloquent 모델
│   ├── Services/           # 비즈니스 로직
│   └── Enums/             # 열거형
├── database/
│   ├── migrations/        # 데이터베이스 마이그레이션
│   └── seeders/          # 시드 데이터
├── resources/
│   ├── views/            # Blade 템플릿
│   └── js/              # Vue.js 컴포넌트
└── routes/
    ├── web.php           # 웹 라우트
    ├── api.php           # API 라우트
    └── channels.php      # 브로드캐스트 채널
```

## 라이선스

MIT License
