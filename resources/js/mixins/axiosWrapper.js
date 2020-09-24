import axios from 'axios';

/* Wrap axios in a function that will retry on timouts and handle auth gracefully */
//TODO: Redirect to the correct auth page on authentication failure.

const defaultOptions = {
    headers: {
        'Content-Type': "application/json",
        'Accept': "application/json",
    }
}

const axiosWrapper = async function(call, url, data, options, retries = 5 ) {
    if( !axios[call] ){
        const error = `No method exists for attempted axios call: ${call}`;
        console.error( error );
        return Promise.reject( error );
    }

    let response;
    while( retries-- > 0 ){
        try {
            let params = [url, data, {...options, ...defaultOptions}];
            return await axios[call](...params);
        } catch( error ) {
            if ( error.code === 'ECONNABORTED' || error.response.status === 408 ) { // Request timed out
                continue;   // Try again
            }

            if ( error.response ) {
                /*We were able to send data, but the server could not process it. */

                console.error( "Request could not be handled by endpoint: ",
                    error.request, error.response, error.header, error.code || "" );

                break; //No point in retrying this request.
            };

            // If we get here, something happened in setting up the request that triggered an Error
            console.error( 'Request failed - unknown error', error );
            break; //Treat as unrecoverable - give up
        }

        return response ? Promise.reject( response ) : { "message" : "Request failed or timed out" };
    }
}

/* Wrappers for common HTTP verbs */
const get = ( url, retries = 5) => axiosWrapper('get', url, retries );
const put = ( url, data, retries = 5 ) => axiosWrapper('put', url, data, retries );
const post = ( url, data, retries = 5 ) => axiosWrapper('post', url, data, retries );
const patch = ( url, data, retries = 5 ) => axiosWrapper('patch', url, retries, data );
const Delete = (url, retries =5 ) =>  axiosWrapper('delete', url, retries );

export const wrappers = () => {
    return {
        get,
        post,
        put,
        patch,
        delete: "Delete"
    };
}

export default { axiosWrapper };
