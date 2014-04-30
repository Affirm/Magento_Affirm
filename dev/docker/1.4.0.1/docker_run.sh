
ID=$(docker ps | grep 8080)

if [ -n "$ID" ]; then
    docker kill $(docker ps | grep 8080 | awk '{ print $1 }')
fi

docker run -d -p 8080:80 magento:1.4.0.1 $*
