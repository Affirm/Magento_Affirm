#! /bin/bash
#

# Usage: sh kill.sh 8080

ID=$(docker ps | grep 8080)

if [ -n "$ID" ]; then
    docker kill $(docker ps | grep 8080 | awk '{ print $1 }')
fi
