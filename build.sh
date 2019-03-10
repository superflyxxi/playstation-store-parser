#!/bin/bash

find ./ -name "*.php" | xargs -L1 php -l

