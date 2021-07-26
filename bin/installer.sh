#!/usr/bin/env bash

set -eux

version=1.4.0

if ! command -v unzip &> /dev/null
then
    echo "Unzip is not installed"
    exit 0
fi

if ! command -v composer &> /dev/null
then
    echo "Composer is not installed"
    exit 0
fi

if ! command -v php &> /dev/null
then
    echo "PHP is not installed"
    exit 0
fi

if ! command -v docker &> /dev/null
then
    echo "Docker is not installed"
    exit 0
fi

if [ -d $HOME/.koala ]; then
    echo "Koala is already installed, please use update"
    exit 0
fi

cd $HOME
wget -qO koala-tmp.zip https://github.com/LarsNieuwenhuizen/koala/archive/refs/tags/$version.zip
unzip koala-tmp.zip
mv koala-$version $HOME/.koala-$version

if [ -L $HOME/.koala ]; then
    rm $HOME/.koala
fi

ln -s $HOME/.koala-$version .koala

rm koala-tmp.zip

cd $HOME/.koala
composer install -o --no-progress --no-interaction

if [ ! -d "$HOME/.local/koala/bin" ]; then
    mkdir -p $HOME/.local/koala/bin
fi

if [[ "$OSTYPE" == "darwin"* ]]; then
    echo export PATH="\"$HOME/.local/koala/bin:\$PATH\"" >> $HOME/.zshrc
else
    echo export PATH="\"$HOME/.local/koala/bin:\$PATH\"" >> $HOME/.bashrc
fi

ln -s $HOME/.koala/bin/koala.php $HOME/.local/koala/bin/koala
