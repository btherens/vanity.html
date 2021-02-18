/* 
 * send web request to server
 * .then() will run when request succeeds
 * .catch() will run when request fails
 */
function appWebRequest(uri, params = null, ispost = false) {
    var options = {
        method: ispost ? 'POST' : 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
        }
    }

    /* generate params string if params exist */
    if (params !== null) {
        if (ispost) {
            options.body = Object.keys(params).map(key => key + '=' + params[key]).join('&');
        } else {
            uri += '?' + new URLSearchParams(params);
        }
    }
    return fetch(uri, options).then(function (response) {
        if (response.ok) {
            return response.json();
        } else {
            return Promise.reject('api request failed. ¯\_(ツ)_/¯');
        }
    });
}