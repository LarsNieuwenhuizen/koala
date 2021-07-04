#!/usr/bin/env bash

set -eux

version=1.0.0

if ! command -v unzip &> /dev/null
then
    echo "Unzip is not installed"
    exit 0
fi

cd $HOME
wget -qO koala-tmp.zip https://github.com/LarsNieuwenhuizen/koala/archive/refs/tags/$version.zip
unzip koala-tmp.zip
mv koala-$version $HOME/.koala
rm koala-tmp.zip
cp $HOME/.koala/bin/koala.sh $HOME/.local/bin/koala
