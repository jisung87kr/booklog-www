<x-bottom-navigation></x-bottom-navigation>
<footer class="py-5 mb-16 md:mb-0">
    <div class="flex flex-wrap gap-3 justify-center text-sm text-gray-500">
        <a href="">@ {{ date('Y') }} {{ config('app.name') }}</a>
        <a href="{{ route('terms') }}">약관</a>
        <a href="{{ route('privacy') }}">개인정보처리방침</a>
        <a href="{{ route('cookie-policy') }}">쿠키 정책</a>
        <a href="{{ route('report-issue') }}">문제 신고</a>
    </div>
</footer>
