#!/usr/bin/env bash

pwd=$1

if [ ! -f $pwd/.env.koala ]; then
    touch $pwd/.env.koala
fi

cp $pwd/.env.dist $pwd/.env.koala

vars=$(printenv)

for var in $vars; do
    if [[ $var == KOALA_* ]]; then
        echo $var >> $pwd/.env.koala
    fi
done
