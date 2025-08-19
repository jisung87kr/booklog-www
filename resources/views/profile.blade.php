@section('title', $user->name . '님의 프로필 - 북로그')
@section('description', $user->name . '님의 독서 기록과 서재를 확인해보세요. 북로그에서 ' . $user->name . '님이 읽은 책과 리뷰를 둘러보며 새로운 책을 발견해보세요.')
@section('keywords', $user->name . ', 프로필, 독서기록, 서재, 책리뷰, ' . ($user->username ? '@' . $user->username : ''))
@section('og_type', 'profile')
@section('og_title', $user->name . '님의 프로필 - 북로그')
@section('og_description', $user->name . '님의 독서 기록과 서재를 확인해보세요.')
@section('og_image', $user->profile_photo_url ?? asset('images/default-profile.jpg'))

@push('meta')
<!-- JSON-LD Structured Data for Profile -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ProfilePage",
    "mainEntity": {
        "@type": "Person",
        "name": "{{ $user->name }}",
        @if($user->username)
        "alternateName": "@{{ $user->username }}",
        @endif
        "url": "{{ route('profile', $user->username ?? $user->id) }}",
        @if($user->introduction)
        "description": "{{ Str::limit($user->introduction, 160) }}",
        @endif
        "image": "{{ $user->profile_photo_url ?? asset('images/default-profile.jpg') }}",
        "sameAs": [
            "{{ route('profile', $user->username ?? $user->id) }}"
        ],
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ route('profile', $user->username ?? $user->id) }}"
        }
    },
    "dateCreated": "{{ $user->created_at->toISOString() }}",
    "dateModified": "{{ $user->updated_at->toISOString() }}"
}
</script>
@endpush

@prepend('scripts')
<script>
    window.__profileUser = @json($user);
</script>
@endprepend
<x-app-layout>
    <suspense>
        <profile-component></profile-component>
    </suspense>
</x-app-layout>
