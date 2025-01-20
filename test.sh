#!/bin/bash

# Version configurations
declare -A versions=(
    ["4.8-php7.3"]="psr-swoole-test-php73"
    ["4.8-php7.4"]="psr-swoole-test-php74" 
    ["5.1-php8.0"]="psr-swoole-test-php80"
    ["5.1-php8.1"]="psr-swoole-test-php81"
    ["5.1-php8.2"]="psr-swoole-test-php82"
    ["6.0-php8.3"]="psr-swoole-test-php83"
    ["6.0-php8.4"]="psr-swoole-test-php84"
)

# Get script directory
SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"

# Check if path parameter is provided
if [ -z "$1" ]; then
    echo "Usage: $0 <path-to-psr-swoole-native> [version]"
    echo "Available versions: ${!versions[@]}"
    exit 1
fi

# Validate directory exists
if [ ! -d "$1" ]; then
    echo "Error: Directory $1 does not exist"
    exit 1
fi

# Move to specified directory
cd "$1" || exit 1

# If version specified, only build that one
if [ ! -z "$2" ]; then
    if [ -v "versions[$2]" ]; then
        docker build -t "${versions[$2]}" --build-arg PHP_VERSION="$2" -f "${SCRIPT_DIR}/Dockerfile" .
        docker run -it --rm "${versions[$2]}"
        exit 0
    else
        echo "Invalid version: $2"
    fi
else
    echo "Pick a valid version"
fi

echo "Available versions: ${!versions[@]}"
exit 1
