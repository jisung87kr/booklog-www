<template>
    <button type="button" @click="copyUrl(feed)">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="20"
             height="20"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round"
             stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M10 14l11 -11"/>
            <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"/>
        </svg>
    </button>
</template>
<script>
export default{
    name: 'ShareButton',
    props: {
        feed: {
            type: Object,
            required: true
        }
    },
    methods: {
        copyUrl(feed){
            const url = window.location.origin + '/feeds/' + feed.id;
            const textarea = document.createElement('textarea');
            textarea.value = url;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert('URL이 복사되었습니다.');
        }
    }
}
</script>
