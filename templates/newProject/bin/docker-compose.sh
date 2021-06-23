#!/usr/bin/env bash
source ~/.koala/.koala
if [[ -f ~/.koala/.koala.local ]]; then
    source ~/.koala/.koala.local
fi

if [[ -f .koala.local ]]; then
    source .koala.local
fi

if [[ -z "{$SSH_AUTH_SOCK}" ]]; then
    ssh-agent
fi

~/.koala/bin/generate-local-env.sh $(pwd)

LOCAL_USER_ID=$(id -u) \
    LOCAL_GROUP_ID=$(id -g) \
    KOALA_PROJECT_DIR=$(pwd) \
    docker-compose $@
