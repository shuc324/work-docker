#!/bin/bash
if [ -f /var/lib/mongodb/mongod.lock ]; then
    rm -rf /var/lib/mongodb/mongod.lock
fi

# 启动mongodb
/usr/bin/mongod -f /etc/mongod.conf &

# 判断是否设置过密码
if [ ! -f /data/mongodb/.setted_mongodb_password ]; then
    USER=${MONGODB_USER:-"admin"}
    DATABASE=${MONGODB_DATABASE:-"root"}
    PASS=${MONGODB_PASS:-123456}
    
    # 设置admin密码
    mongo admin --eval "db.createUser({user: '$USER', pwd: '$PASS', roles:[{role:'root',db:'admin'}]});"
    
    if [ "$DATABASE" != "admin" ]; then
        mongo admin -u $USER -p $PASS << EOF
use $DATABASE
db.createUser({user: '$USER', pwd: '$PASS', roles:[{role:'dbOwner',db:'$DATABASE'}]})
EOF
    fi

    touch /data/mongodb/.setted_mongodb_password
fi
