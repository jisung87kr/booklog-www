
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
