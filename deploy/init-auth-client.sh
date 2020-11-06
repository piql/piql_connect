#!/bin/bash

current_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
env_file="$current_dir/../.env"
if [[ -f "$env_file" ]]; then
    source $env_file
fi
realm=${AUTH_REALM:-'development'}
url=${AUTH_BASE_URL:-'https://auth.piqlconnect.com/auth/'}
clientId=${AUTH_CLIENT:-'piql-connect-frontend'}
testUrl=${APP_FE_URL:-'http://localhost/'}

sed -e "s|process.env.AUTH_REALM|'$realm'|g" \
    -e "s|process.env.AUTH_BASE_URL|'$url'|g" \
    -e "s|process.env.AUTH_CLIENT|'$clientId'|g" \
    -e "s|process.env.APP_FE_URL|'$testUrl'|g" \
    $current_dir/../resources/js/environment.js.template >\
    $current_dir/../resources/js/environment.js || exit $?

