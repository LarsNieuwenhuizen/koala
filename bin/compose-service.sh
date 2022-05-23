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

services=()
# Load all available subsirectories of services/ into an array
for directory in $HOME/.koala/services/* ; do
    services+=(${directory##*/})
done

# Present selection menu
PS3="which service do you want? "
echo "There are ${#services[@]} known services"; \
select option in "${services[@]}"; do
    service=${option}
    break;
done

if [ -f $HOME/.koala/services/${service}/init.sh ]; then
    $HOME/.koala/services/${service}/init.sh
fi
