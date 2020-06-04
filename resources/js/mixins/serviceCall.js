export default {
    methods: {
        get: async function(url, def = { retry : 5}) {
            return this.doServiceRequest(function() {
                return axios.get(url);
            }, def);
        },

        // do the same for post
        post: async function(url, payload, def = { retry : 1} ) {
            return this.doServiceRequest(function() {
                return axios.post(url, payload);
            }, def);
        },

        // do the same for delete
        delete: async function(url, def = { retry : 1} ) {
            return this.doServiceRequest(function() {
                return axios.delete(url);
            }, def);
        },

        // do the same for post
        patch: async function(url, payload, def = { retry : 5} ) {
            return this.doServiceRequest(function() {
                return axios.patch(url, payload);
            }, def);
        },

        doServiceRequest : async function(createServiceRequest, def = { retry : 5}) {

            let retry = 1;
            if(def.retry !== undefined) {
                retry = def.retry;
            }

            let response = ""
            while(retry--) {
                try {
                    return await createServiceRequest();
                } catch(error) {
                    if (error.response) {
                        // The request was made and the server responded with a status code
                        // that falls out of the range of 2xx
                        if(error.response.status == 401) {
                            Vue.nextTick( () => { window.location = "/logout"; } );
                        } else if(error.response.status >= 500) {
                            continue;
                        }
                        response = error.response.data;
                        console.log(error.response.data);
                        console.log(error.response.status);
                        console.log(error.response.headers);
                    } else if (error.request) {
                        // The request was made but no response was received
                        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                        // http.ClientRequest in node.js
                        console.log(error.request);
                        continue;
                    } else {
                        // Something happened in setting up the request that triggered an Error
                        console.log('Error', error.message);
                    }
                    console.log(error.config);
                    break;
                }
            }
            if(response === "")
                response = {"message" : "Request failed or timed out"};

            return Promise.reject(response);
        }
    }
}
