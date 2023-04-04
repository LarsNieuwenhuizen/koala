#!/usr/bin/env bash

pwd=$1
cp $pwd/.env.dist $pwd/.env.koala

vars=$(printenv)

for var in $vars; do
    if [[ $var == KOALA_* ]]; then
        echo $var >> $pwd/.env.koala
    fi
done
