#!/bin/bash
echo "Creating overrides"
env | while IFS='=' read -r env val; do 
  if [[ "$env" == PSSTORE_* ]]; then
    env=$(echo ${env} | sed 's/_/./g' | sed 's/PSSTORE.\(.*\)/\L\1/g')
    echo "${env}=${val}" >> /home/ps-store/resources/settings_override.ini
  fi
done
cat /home/ps-store/resources/settings_override.ini
