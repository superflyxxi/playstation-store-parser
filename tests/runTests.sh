#!/bin/bash

cp ../src/settings.ini ./

find ./ -name "*.php" | sort | xargs -L1 php 

