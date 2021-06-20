#!/usr/bin/env bash
~/.koala/bin/docker-compose.sh -f ~/.koala/docker-compose.yaml stop

docker stop $(docker network inspect koala --format='{{range $id, $_ := .Containers}}{{println $id}}{{end}}')
