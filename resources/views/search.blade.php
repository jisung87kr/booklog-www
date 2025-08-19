@section('title', '검색 - 북로그')
@section('description', '북로그에서 원하는 책, 사용자, 리뷰를 검색해보세요. 키워드로 쉽게 찾고, 새로운 독서 경험을 발견하세요.')
@section('keywords', '검색, 책검색, 사용자검색, 리뷰검색, 도서찾기, 독서')
@section('og_title', '검색 - 북로그')
@section('og_description', '북로그에서 원하는 책, 사용자, 리뷰를 검색해보세요.')

@push('meta')
<!-- JSON-LD Structured Data for Search -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SearchResultsPage",
    "url": "{{ request()->url() }}",
    "name": "검색 - 북로그",
    "description": "북로그에서 원하는 책, 사용자, 리뷰를 검색해보세요.",
    "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ route('search.index') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>
@endpush

<x-app-layout>
    <suspense>
        <search-component></search-component>
    </suspense>
{{--    <x-footer></x-footer>--}}
</x-app-layout>
