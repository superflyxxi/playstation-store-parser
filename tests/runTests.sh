#!/bin/bash

cp ../src/settings.ini ./

find ./ -name "*.php" | xargs -L1 php 

