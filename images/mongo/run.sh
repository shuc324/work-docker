#!/bin/bash
if [ -f /data/db/mongod.lock ]; then
    rm -rf /data/db/mongod.lock
fi

/usr/bin/mongod -f /etc/mongod.conf &

RET=1
while [[ RET -ne 0 ]]; do
    sleep 5
    mongo admin --eval "help" >/dev/null 2>&1
    RET=$?
done

if [ ! -f /data/db/.setted_mongodb_password ]; then
    USER=${MONGODB_USER:-"admin"}
    DATABASE=${MONGODB_DATABASE:-"admin"}
    PASS=${MONGODB_PASS:-"123456"}
    mongo admin --eval "db.createUser({user: '$USER', pwd: '$PASS', roles:[{role:'root',db:'admin'}]});"

    if [ "$DATABASE" != "admin" ]; then
        mongo admin -u $USER -p $PASS << EOF
use $DATABASE
db.createUser({user: '$USER', pwd: '$PASS', roles:[{role:'dbOwner',db:'$DATABASE'}]})
EOF
    fi

    touch /data/db/.setted_mongodb_password
fi
