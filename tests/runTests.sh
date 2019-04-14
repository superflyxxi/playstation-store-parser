#!/bin/bash

cp -v settings_override.ini ../src/

find ./ -name "*.php" | sort | xargs -L1 php 

