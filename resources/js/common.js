
export async function sendRequest(method, url, data= {}, headers={}, callback=null){
    try {
        let options = {
            url: url,
            method: method,
            headers: headers
        };

        if (method.toUpperCase() === 'GET') {
            options.params = data;
        } else {
            options.data = data;
        }

        let response = await window.axios(options);

        if (response.data.status !== true) {
            throw new Error(response.data.message);
        }

        if (callback && typeof callback === 'function') {
            callback(response.data);
        }

        return response.data;

    } catch (error) {
        if (error.response?.data?.data) {
            alert(error.response.data.data);
        } else {
            alert(error.message);
        }
        throw error;
    }
}

export function copyText(text){
    if(text === ''){
        alert('복사할 대상이 없습니다.');
    }
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    alert('복사되었습니다.');
}


export function getHashTags(text){
    return text.match(/#[a-zA-Z0-9ㄱ-ㅎ가-힣\s]+/g);
}

export function getBooksTags(text){
    return text.match(/\$[a-zA-Z0-9ㄱ-ㅎ가-힣\s]+/g);
}

export function getMentions(text){
    return text.match(/@[a-zA-Z0-9ㄱ-ㅎ가-힣\s]+/g);
}


export function wrapWithSpan(text){

    // 정규식 패턴 정의
    const hashTagPattern = /#[a-zA-Z0-9ㄱ-ㅎ가-힣\s]+/g;
    const bookPattern = /\$[a-zA-Z0-9ㄱ-ㅎ가-힣\s]+/g;
    const mentionPattern = /@[a-zA-Z0-9ㄱ-ㅎ가-힣\s]+/g;

    // 탐색하고 감싸기
    const wrappedText = text
        .replace(hashTagPattern, match => `<a href="/search?q=${match.replace('#', '')}" class="tagbox hashtag">${match}</a>`)
        .replace(bookPattern, match => `<a href="/search?q=${match.replace('$', '')}" class="tagbox booktag">${match}</a>`)
        .replace(mentionPattern, match => `<a href="/search?q=${match.replace('@', '')}" class="tagbox mention">${match}</a>`);

    return wrappedText;
}

export function jsonToFormData(data, formData = new FormData(), parentKey = '') {
    if (typeof data === 'object' && data !== null) {
        Object.keys(data).forEach(key => {
            const fullKey = parentKey ? `${parentKey}[${key}]` : key;
            const value = data[key];

            if (value === null) {
                // null 값을 건너뜁니다.
                return;
            }

            if (value instanceof FileList) {
                // FileList를 배열로 변환하여 처리
                Array.from(value).forEach((file, index) => {
                    formData.append(`${fullKey}[${index}]`, file);
                });
            } else if (Array.isArray(value)) {
                // 배열인 경우, 각 요소를 처리
                value.forEach((item, index) => {
                    jsonToFormData(item, formData, `${fullKey}[${index}]`);
                });
            } else if (typeof value === 'object') {
                // 객체인 경우, 재귀적으로 호출
                jsonToFormData(value, formData, fullKey);
            } else {
                // 기본 타입 (문자열, 숫자 등)
                formData.append(fullKey, value);
            }
        });
    } else if (data !== null) {
        // 기본 타입이면서 null이 아닌 경우에만 추가
        formData.append(parentKey, data);
    }
    return formData;
}
