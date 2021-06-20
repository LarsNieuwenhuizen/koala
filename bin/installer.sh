#!/usr/bin/env bash

set -eux
cd ~
git clone https://github.com/LarsNieuwenhuizen/koala.git .koala
cp ~/.koala/bin/koala.sh ~/.local/bin/koala
