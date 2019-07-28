#!/bin/bash

cp -v settings_override.ini ../resources/

find ./ -name "*.php" | sort | xargs -L1 php 

