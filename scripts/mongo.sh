#!/bin/bash
docker ps|grep mongo|awk '{print $1}'|xargs docker rm -f && docker images|grep mongo|awk '{print $3}'|xargs docker rmi -f && docker-compose build mongo && docker-compose up -d mongo
