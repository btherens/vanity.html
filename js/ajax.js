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

    /* add parameters to request if any were passed */
    if (params !== null) {
        /* add to body for post requests */
        if (ispost) { options.body = Object.keys(params).map(key => key + '=' + params[key]).join('&') }
        /* otherwise, add GET parameters to querystring */
        else { uri += '?' + new URLSearchParams(params) }
    }
    /* execute using fetch */
    return fetch(uri, options).then(function (response) {
        /* validate server response before returning */
        if (response.ok) { return response.json() } else { return Promise.reject('api request failed. ¯\_(ツ)_/¯') }
    });
}