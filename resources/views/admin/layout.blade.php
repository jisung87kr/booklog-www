<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', '관리자 페이지') - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Vue.js와 Axios는 각 페이지에서 필요시 로드 -->
</head>
<body class="bg-gray-50">
    <!-- 모바일 오버레이 -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden"></div>

    <div class="min-h-screen flex">
        <!-- 사이드바 -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="h-full flex flex-col">
                <!-- 헤더 -->
                <div class="p-4 lg:p-6 border-b flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <h1 class="text-lg lg:text-xl font-bold text-gray-800">
                            <i class="fas fa-cog text-primary-500 mr-2"></i>
                            <span class="hidden sm:inline">관리자 페이지</span>
                            <span class="sm:hidden">관리자</span>
                        </h1>
                        <!-- 모바일 닫기 버튼 -->
                        <button id="close-sidebar" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- 스크롤 가능한 네비게이션 영역 -->
                <nav class="flex-1 overflow-y-auto py-6">
                    <x-admin.menu-section title="메뉴" />

                    <div class="space-y-1">
                        <x-admin.menu-item
                            route="admin.dashboard"
                            route-pattern="admin.dashboard"
                            icon="fas fa-chart-line"
                            label="대시보드" />

                        <x-admin.menu-item
                            route="admin.personas"
                            route-pattern="admin.personas"
                            icon="fas fa-user-friends"
                            label="페르소나 관리" />

                        <x-admin.menu-item
                            route="admin.users"
                            route-pattern="admin.users"
                            icon="fas fa-users"
                            label="사용자 관리" />

                        <x-admin.menu-item
                            route="admin.categories"
                            route-pattern="admin.categories*"
                            icon="fas fa-tags"
                            label="카테고리 관리" />

                        <x-admin.menu-item
                            route="admin.contacts.index"
                            route-pattern="admin.contacts*"
                            icon="fas fa-envelope"
                            label="문의 관리" />

                        <x-admin.menu-dropdown
                            icon="fas fa-file-alt"
                            label="콘텐츠 관리"
                            route-pattern="admin.feeds*">
                            <x-admin.menu-item
                                route="admin.feeds"
                                route-pattern="admin.feeds*"
                                icon="fas fa-rss"
                                label="피드 관리" />
                            <x-admin.menu-item
                                route="admin.posts"
                                route-pattern="admin.posts*"
                                icon="fas fa-file-alt"
                                label="포스트 관리" />
                        </x-admin.menu-dropdown>
                    </div>

                    <x-admin.menu-section title="작업" class="mt-8" />

                    <div class="space-y-1">
                        <form action="{{ route('admin.generate-feeds') }}" method="POST" class="w-full">
                            @csrf
                            <x-admin.menu-item
                                type="button"
                                icon="fas fa-magic"
                                label="AI 피드 생성" />
                        </form>
                    </div>
                </nav>

                <!-- 하단 사용자 정보 고정 -->
                <div class="flex-shrink-0 p-6 border-t bg-white">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</p>
                            <p class="text-xs text-gray-500">관리자</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- 메인 콘텐츠 -->
        <main class="flex-1 lg:ml-64 min-w-0">
            <!-- 헤더 -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-4 lg:px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <!-- 모바일 메뉴 버튼 -->
                            <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 mr-3">
                                <i class="fas fa-bars"></i>
                            </button>
                            <div>
                                <h2 class="text-xl lg:text-2xl font-bold text-gray-900">@yield('header', '대시보드')</h2>
                                <p class="text-xs lg:text-sm text-gray-600 mt-1 hidden sm:block">@yield('description', '시스템 현황을 확인하세요')</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 lg:space-x-4">
                            <div class="relative hidden sm:block">
                                <i class="fas fa-bell text-gray-400 hover:text-gray-600 cursor-pointer"></i>
                            </div>

                            <a href="{{ route('home') }}" class="inline-flex items-center px-3 lg:px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs lg:text-sm font-medium rounded-lg transition-colors">
                                <i class="fas fa-home mr-1 lg:mr-2"></i>
                                <span class="hidden sm:inline">사이트로 돌아가기</span>
                                <span class="sm:hidden">홈</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- 알림 메시지 -->
            @if(session('success'))
            <div class="mx-6 mt-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg" role="alert">
                <div class="flex">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-6 mt-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                <div class="flex">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <!-- 페이지 콘텐츠 -->
            <div class="p-4 lg:p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // 모바일 메뉴 토글
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const closeSidebar = document.getElementById('close-sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            mobileOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebarFunc() {
            sidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
            document.body.style.overflow = '';
        }

        mobileMenuBtn?.addEventListener('click', openSidebar);
        closeSidebar?.addEventListener('click', closeSidebarFunc);
        mobileOverlay?.addEventListener('click', closeSidebarFunc);

        // 링크 클릭 시 모바일에서 사이드바 닫기
        document.querySelectorAll('.admin-nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeSidebarFunc();
                }
            });
        });

        // 창 크기 변경 시 사이드바 상태 초기화
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                mobileOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });

        // 드롭다운 토글 함수
        function toggleDropdown(button) {
            const dropdown = button.parentElement;
            const content = dropdown.querySelector('.dropdown-content');
            const chevron = dropdown.querySelector('.dropdown-chevron');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        }

        // 페이지 로드 시 활성 메뉴의 드롭다운 열기
        document.addEventListener('DOMContentLoaded', function() {
            const activeDropdowns = document.querySelectorAll('.admin-dropdown');
            activeDropdowns.forEach(dropdown => {
                const routePattern = dropdown.dataset.routePattern;
                const content = dropdown.querySelector('.dropdown-content');
                const chevron = dropdown.querySelector('.dropdown-chevron');
                const button = dropdown.querySelector('button');

                // 현재 페이지가 해당 라우트 패턴과 일치하거나 하위 메뉴 중 활성 항목이 있는 경우
                if (button.classList.contains('active') || content.querySelector('.admin-nav-link.active')) {
                    content.classList.remove('hidden');
                    chevron.classList.add('rotate-180');
                }
            });
        });
    </script>

    <style>
        .admin-nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #6b7280;
            transition: all 0.2s ease-in-out;
            border-radius: 0;
            text-decoration: none;
        }

        .admin-nav-link:hover {
            background-color: #f9fafb;
            color: #111827;
        }

        .admin-nav-link.active {
            background-color: #eff6ff;
            color: #1d4ed8;
            border-right: 3px solid #2563eb;
            font-weight: 600;
        }

        .admin-nav-link i {
            margin-right: 0.75rem;
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-nav-button {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #059669;
            transition: all 0.2s ease-in-out;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }

        .admin-nav-button:hover {
            background-color: #ecfdf5;
            color: #047857;
        }

        .admin-nav-button i {
            margin-right: 0.75rem;
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    @stack('scripts')
</body>
</html>
