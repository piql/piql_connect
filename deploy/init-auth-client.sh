#!/bin/bash

while getopts r: flag
do  
  case "${flag}" in
    r) KEYCLOAK_REALM_NAME=${OPTARG};;
  esac
done

current_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
env_file="$current_dir/../.env"
if [[ -f "$env_file" ]]; then
    source $env_file
fi
realm=${AUTH_REALM:-$KEYCLOAK_REALM_NAME}
url=${AUTH_BASE_URL:-'https://auth.piqlconnect.com/auth/'}
clientId=${AUTH_CLIENT:-'piql-connect-frontend'}

sed -e "s|process.env.AUTH_REALM|'$realm'|g" \
    -e "s|process.env.AUTH_BASE_URL|'$url'|g" \
    -e "s|process.env.AUTH_CLIENT|'$clientId'|g" \
    $current_dir/../resources/js/environment.js.template >\
    $current_dir/../resources/js/environment.js || exit $?

