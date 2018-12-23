#!/bin/bash

METACRITIC_API_SRC_URL=${METACRITIC_API_SRC_URL:-"https://github.com/superflyxxi/metacritic_api/archive/"}
METACRITIC_API_VERSION=${METACRITIC_API_VERSION:-"v1.1"}

cd ../src
wget -nc ${METACRITIC_API_SRC_URL}${METACRITIC_API_VERSION}.tar.gz
tar xf ${METACRITIC_API_VERSION}.tar.gz
cd ../tests

cp ../src/settings.ini ./

find ./ -name "*.php" | xargs -L1 php 

