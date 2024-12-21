# psr-swoole-native-tests

This repository contains the tools to test [imefisto/psr-swoole-native] package on different setups.

## Usage for testing a local version of psr-swoole-native

1. Clone this repository
2. Run `./test.sh` with no parameters to see available versions of Swoole and PHP.
3. Run `./test.sh /path/to/psr-swoole-native 5.1-php-8.2`

This should trigger a docker build and run the tests on a container.
