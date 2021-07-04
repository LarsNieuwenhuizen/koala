#!/usr/bin/env bash
$HOME/.koala/bin/docker-compose.sh -f $HOME/.koala/docker-compose.yaml stop

containers=$(docker network inspect koala --format='{{range $id, $_ := .Containers}}{{println $id}}{{end}}')

if [ -n "$containers" ]; then
    docker stop $containers
fi
