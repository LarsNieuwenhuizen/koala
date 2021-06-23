#!/usr/bin/env bash

command="$@"

status=$(bin/docker-compose.sh ps | grep php)
if [[ -z "${status}" ]]; then
    bin/docker-compose.sh run --rm --entrypoint /opt/local/entrypoints/entrypoint-shell.sh php composer "${command}"
else
    bin/docker-compose.sh exec php composer "${command}"
fi
