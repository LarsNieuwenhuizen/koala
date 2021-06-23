#!/usr/bin/env bash
~/.koala/bin/start-environment.sh

source ~/.koala/.koala
if [[ -f ~/.koala/.koala.local ]]; then
    source ~/.koala/.koala.local
fi

if [[ -f .koala.local ]]; then
    source .koala.local
fi

source .env.dist
~/.koala/bin/generate-certificates.sh ${COMPOSE_PROJECT_NAME}.${KOALA_LOCAL_TLD}
bin/docker-compose.sh up -d
