//CREATE ENVIRONMENT VARIABLES AS OBJECTS OR VARIABLES 
//AND ADD THEM TO THE EXPORTED OBJECT TO BE USED IN APP.JS

const keyCloakConfig = {
    realm: 'development',
    url: 'https://auth.piqlconnect.com/auth/',
    clientId: 'piql-connect-frontend',
}


export default {
    keyCloakConfig
}