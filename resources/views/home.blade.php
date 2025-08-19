@section('title', '북로그 - 독서 기록과 소통의 플랫폼')
@section('description', '북로그에서 독서 기록을 남기고, 책 리뷰를 공유하며, 독서 커뮤니티와 소통해보세요. AI가 추천하는 맞춤형 도서와 함께 더 풍부한 독서 경험을 만들어보세요.')
@section('keywords', '독서, 책리뷰, 독서기록, 북로그, 도서추천, 독서커뮤니티, 책추천, 서재, 독서일기, AI추천')
@section('og_type', 'website')

@push('meta')
<!-- JSON-LD Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "북로그",
    "alternateName": "BookLog",
    "url": "{{ url('/') }}",
    "description": "독서 기록과 소통의 플랫폼",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ route('search.index') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
    },
    "author": {
        "@type": "Organization",
        "name": "BookLog"
    }
}
</script>
@endpush

<x-app-layout>
    <suspense>
        <home-component></home-component>
    </suspense>
</x-app-layout>
