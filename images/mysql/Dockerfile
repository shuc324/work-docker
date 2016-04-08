FROM ubuntu
MAINTAINER Shu Chao<shuc324.gmail.com>

# 163源
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty main restricted universe multiverse" > /etc/apt/sources.list
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-security main restricted universe multiverse" >> /etc/apt/sources.list
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-updates main restricted universe multiverse" >> /etc/apt/sources.list
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-proposed main restricted universe multiverse" >> /etc/apt/sources.list
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-backports main restricted universe multiverse" >> /etc/apt/sources.list

# 安装包
ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && apt-get -y upgrade
RUN apt-get install -y mysql-server
RUN rm -rf /var/lib/apt/lists/* 

# 添加配置
RUN sed -Ei 's/^(bind-address)/#&/' /etc/mysql/my.cnf
RUN { \
    echo '[mysqld]'; \
        echo 'console=1'; \
            echo 'general_log=1'; \
                echo 'general_log_file=/dev/stdout'; \
                    echo 'log_error=/dev/stderr'; \
} > /etc/mysql/conf.d/my.cnf

ADD run.sh /usr/local/bin/run.sh
RUN chmod +x /usr/local/bin/run.sh

EXPOSE 3306

CMD ["/usr/local/bin/run.sh"]
