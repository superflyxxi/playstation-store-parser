#!/bin/bash

METACRITIC_API_SRC_URL=${METACRITIC_API_SRC_URL:-"https://github.com/superflyxxi/metacritic_api/archive/"}
METACRITIC_API_VERSION=${METACRITIC_API_VERSION:-"v1.1"}

find ./ -name "*.php" | xargs -L1 php -l

cd src
wget -nc ${METACRITIC_API_SRC_URL}${METACRITIC_API_VERSION}.tar.gz
tar -vxf ${METACRITIC_API_VERSION}.tar.gz
rm -v ${METACRITIC_API_VERSION}.tar.gz
cd ..

