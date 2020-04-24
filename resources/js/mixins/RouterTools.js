export default {
    methods: {
        /*
         * updateQueryParams adds/updates query parameters in the url
         *
         * @params is an object and sets its key/value pairs in the browser url query string
         *
         */
        updateQueryParams( params ) {
            Object
                .entries( params )
                .map( param => {
                    const name = param[0];
                    const value = param[1];
                    const query = Object.assign( {}, this.$route.query );
                    if ( value && value != query[name] ) {
                        query[name] = encodeURI( value );
                        this.$router.push({ query });
                    } 
                    
                    if( !value && query[name] ) {
                        delete query[name];
                        this.$router.replace({ query });
                    }
                });
        }
    }
}
