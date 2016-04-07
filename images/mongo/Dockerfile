FROM ubuntu:14.04
MAINTAINER Shu Chao<shuc324@gmail.com>

# 163 源
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty main restricted universe multiverse" > /etc/apt/sources.list
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-security main restricted universe multiverse" >> /etc/apt/sources.list
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-updates main restricted universe multiverse" >> /etc/apt/sources.list
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-proposed main restricted universe multiverse" >> /etc/apt/sources.list
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-backports main restricted universe multiverse" >> /etc/apt/sources.list

# 安装mongodb
RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
RUN echo 'deb http://repo.mongodb.org/apt/ubuntu trusty/mongodb-org/3.0 multiverse' | sudo tee /etc/apt/sources.list.d/mongodb-org-3.0.list
RUN apt-get update && apt-get install -y mongodb-org=3.0.0 mongodb-org-server=3.0.0 mongodb-org-shell=3.0.0 mongodb-org-mongos=3.0.0 mongodb-org-tools=3.0.0

RUN rm -rf /var/lib/apt/lists/*
RUN echo "export LC_ALL=\"C\"" >> ~/.bashrc

# 更改配置
RUN sed -Ei 's/^(#port)/port/' /etc/mongod.conf
RUN sed -Ei 's/^(bind_ip)/#&/' /etc/mongod.conf
RUN sed -Ei 's/^(#auth)/auth/' /etc/mongod.conf
RUN sed -Ei 's/^(# nojournal)/nojournal/' /etc/mongod.conf

ADD run.sh /run.sh
RUN chmod 755 /run.sh /data/mongodb /data/db

EXPOSE 27017

CMD ["/run.sh"]
