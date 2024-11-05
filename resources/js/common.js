
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
