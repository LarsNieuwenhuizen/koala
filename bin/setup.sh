#!/usr/bin/env bash
source ~/.koala/.koala
if [[ -f ~/.koala/.koala.local ]]; then
    source ~/.koala/.koala.local
else
    touch ~/.koala/.koala.local
fi

koalaNetworkExists=$(docker network ls  | grep " koala ")

if [[ -z "${koalaNetworkExists}" ]]; then
    docker network create koala
fi

~/.koala/bin/generate-certificates.sh mail.${KOALA_LOCAL_TLD}
