/* Invoke Web Request */
class InvWeb {
    /* handler with fetch API */
    static async _request( url, params = null, method = 'GET' ) {
        /* set request type and headers */
        const options = { method, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' } };
        /* attach parameters */
        if ( params !== null ) {
            /* cast to query string for GET requests */
            if   ( method === 'GET' ) { url += '?' + new URLSearchParams( params ) }
            /* send parameters in request body */
            else { options.body = JSON.stringify( params ) }
        }
        /* await the response */
        const response = await fetch( url, options );
        /* throw server errors */
        if ( response.status !== 200 ) { throw Error( `server status code: ${response.status}` ) }
        /* set response body to return variable */
        const result = await response.json();
        return result;
    }
    /* PUBLIC METHODS */
    static async GET(    url, params ) { return this._request( url, params ) }
    static async POST(   url, params ) { return this._request( url, params, 'POST' ) }
    static async PUT(    url, params ) { return this._request( url, params, 'PUT' ) }
    static async DELETE( url, params ) { return this._request( url, params, 'DELETE' ) }
}
