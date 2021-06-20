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

services=()
# Load all available subsirectories of services/ into an array
for directory in ~/.koala/services/* ; do
    services+=(${directory##*/})
done

# Present selection menu
PS3="which service do you want? "
echo "There are ${#services[@]} known services"; \
select option in "${services[@]}"; do
    service=${option}
    break;
done

if [ -f ~/.koala/services/${service}/init.sh ]; then
    ~/.koala/services/${service}/init.sh
fi

LOCAL_USER_ID=$(id -u) \
    LOCAL_GROUP_ID=$(id -g) \
    docker-compose -f ~/.koala/services/${service}/docker-compose.yaml "$@"
