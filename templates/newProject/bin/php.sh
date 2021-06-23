#!/usr/bin/env bash

command="$@"
bin/docker-compose.sh exec php /usr/bin/php "${command}"
