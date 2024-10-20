
export async function sendRequest(method, url, data= {}, headers={}, callback=null){
    try {
        let response = await window.axios({
           url: url,
           method: method,
           data: data,
           headers: headers
        });

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
