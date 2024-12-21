#!/bin/bash

# Version configurations (same as test.sh)
declare -A versions=(
    ["4.8-php7.3"]="psr-swoole-server-php73"
    ["4.8-php7.4"]="psr-swoole-server-php74" 
    ["5.1-php8.0"]="psr-swoole-server-php80"
    ["5.1-php8.1"]="psr-swoole-server-php81"
    ["5.1-php8.2"]="psr-swoole-server-php82"
    ["6.0-php8.4"]="psr-swoole-server-php84"
)

# Get script directory
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

# Check if path parameter is provided
if [ -z "$1" ]; then
    echo "Usage: $0 [version]"
    echo "Available versions: ${!versions[@]}"
    exit 1
fi

SWOOLE_PHP_VERSION=$1

# If version specified, only build and run that one
if [ ! -z "$SWOOLE_PHP_VERSION" ]; then
    if [ -v "versions[$SWOOLE_PHP_VERSION]" ]; then
        docker build -t "${versions[$SWOOLE_PHP_VERSION]}" --build-arg PHP_VERSION="$SWOOLE_PHP_VERSION" -f "${SCRIPT_DIR}/Dockerfile.server" .
        docker run -p 9501:9501 --rm "${versions[$SWOOLE_PHP_VERSION]}"
        exit 0
    else
        echo "Invalid version: $SWOOLE_PHP_VERSION"
    fi
else
    echo "Pick a valid version"
fi

echo "Available versions: ${!versions[@]}"
exit 1
