#!/usr/bin/env bash
source $HOME/.koala/.koala
if [[ -f $HOME/.koala/.koala.local ]]; then
    source $HOME/.koala/.koala.local
else
    touch $HOME/.koala/.koala.local
fi

koalaNetworkExists=$(docker network ls  | grep " koala ")

if [[ -z "${koalaNetworkExists}" ]]; then
    docker network create koala
fi

$HOME/.koala/bin/generate-certificates.sh mail.${KOALA_LOCAL_TLD}
