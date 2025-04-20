#!/bin/bash

set -o allexport
source .env
set +o allexport

XDEBUG_CONFIG_FILE="./docker/php/conf.d/docker-php-ext-xdebug.ini"

if [ -e "$XDEBUG_CONFIG_FILE" ]; then
    if [ -d "$XDEBUG_CONFIG_FILE" ]; then
        rm -r "$XDEBUG_CONFIG_FILE"
    elif [ -f "$XDEBUG_CONFIG_FILE" ]; then
        rm -f "$XDEBUG_CONFIG_FILE"
    fi
fi

mkdir -p "$(dirname "$XDEBUG_CONFIG_FILE")"

cat <<EOL > "$XDEBUG_CONFIG_FILE"
zend_extension=xdebug
upload_max_filesize = 100M
post_max_size = 108M
xdebug.mode=develop,debug
xdebug.discover_client_host=true
xdebug.start_with_request=yes
xdebug.client_port=9005
xdebug.client_host=${XDEBUG_CLIENT_HOST}
EOL

echo "✅ Arquivo de configuração do Xdebug recriado"
