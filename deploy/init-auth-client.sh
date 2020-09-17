#!/bin/bash

current_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
env_file="$current_dir/../.env"
if [[ -f "$env_file" ]]; then
    source $env_file
fi
realm=${AUTH_REALM:-'development'}
url=${AUTH_BASE_URL:-'https://auth.piqlconnect.com/auth/'}
clientId=${AUTH_CLIENT:-'piql-connect-frontend'}

vue_entry_file=$current_dir/../resources/js/app.js
sed -ie "s|process.env.AUTH_REALM|'$realm'|g" $vue_entry_file
sed -ie "s|process.env.AUTH_BASE_URL|'$url'|g" $vue_entry_file
sed -ie "s|process.env.AUTH_CLIENT|'$clientId'|g" $vue_entry_file