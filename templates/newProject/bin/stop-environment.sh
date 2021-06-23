#!/usr/bin/env bash

networkPrefix=$(bin/docker-compose.sh ps | grep _ | awk '{print $1}' | head -1 | grep -o '^[^_]*')
networkName="${networkPrefix}_default"

bin/docker-compose.sh down
