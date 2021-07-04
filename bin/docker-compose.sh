#!/usr/bin/env bash
cd $HOME/.koala

source $HOME/.koala/.koala
export $(cat $HOME/.koala/.koala) | xargs

if [[ -f $HOME/.koala/.koala.local ]]; then
    source $HOME/.koala/.koala.local
    export $(cat $HOME/.koala/.koala.local) | xargs
fi

if [[ -z "{$SSH_AUTH_SOCK}" ]]; then
    ssh-agent
fi

LOCAL_USER_ID=$(id -u) \
    LOCAL_GROUP_ID=$(id -g) \
    docker-compose -f $HOME/.koala/docker-compose.yaml "$@"
