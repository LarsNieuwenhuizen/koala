#!/usr/bin/env bash
cd ~/.koala

source ~/.koala/.koala
export $(cat ~/.koala/.koala) | xargs

if [[ -f ~/.koala/.koala.local ]]; then
    source ~/.koala/.koala.local
    export $(cat ~/.koala/.koala.local) | xargs
fi

if [[ -z "{$SSH_AUTH_SOCK}" ]]; then
    ssh-agent
fi

LOCAL_USER_ID=$(id -u) \
    LOCAL_GROUP_ID=$(id -g) \
    docker-compose -f ~/.koala/docker-compose.yaml "$@"
