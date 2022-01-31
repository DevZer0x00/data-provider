#!/bin/bash

composer install -n

i=0
until [ $i -ge 20 ]
  do
    nc -z dataprovider_mysql 3306 2>/dev/null && break
    i=$(( i + 1 ))
    echo "$i: Waiting for mysql .."
    sleep 5
done

if [ $i -eq 20 ]; then
  echo "Mysql unavailable, terminating ..."
  exit 1
fi

php vendor/bin/codecept run
