FROM ubuntu
MAINTAINER Shu Chao<shuc324@gmail.com>

# 163 源                                                                                                                                                                                                
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty main restricted universe multiverse" > /etc/apt/sources.list                                                                                        
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-security main restricted universe multiverse" >> /etc/apt/sources.list                                                                              
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-updates main restricted universe multiverse" >> /etc/apt/sources.list                                                                               
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-proposed main restricted universe multiverse" >> /etc/apt/sources.list                                                                              
RUN echo "deb http://mirrors.163.com/ubuntu/ trusty-backports main restricted universe multiverse" >> /etc/apt/sources.list

# 安装包
RUN apt-get update
RUN locale-gen en_US.UTF-8 && export LANG=en_US.UTF-8
RUN apt-get install -y php5-fpm php5-curl php5-mysqlnd php5-mcrypt php5-gd php5-memcached php5-gearman php5-xdebug php5-mongo php5-redis git
RUN rm -rf /var/lib/apt/lists/*

# php 配置 
RUN sed -E -i "s/^listen\ =.+?$/listen = 0.0.0.0:9000/" /etc/php5/fpm/pool.d/www.conf
RUN sed -E -i "s/^pm\ =.+?$/pm = ondemand/" /etc/php5/fpm/pool.d/www.conf
RUN sed -E -i "s/^pm\.max_children\ =.+?$/pm\.max_children = 2/" /etc/php5/fpm/pool.d/www.conf
RUN sed -E -i "s/^;pm\.process_idle_timeout\ =.+?$/pm\.process_idle_timeout=10s/" /etc/php5/fpm/pool.d/www.conf
RUN sed -E -i "s/^error_log\ =.+?$/error_log = \/dev\/stdout/" /etc/php5/fpm/php-fpm.conf
RUN sed -E -i "s/^post_max_size\ =.+?$/post_max_size = 100M/" /etc/php5/fpm/php.ini
RUN sed -E -i "s/^upload_max_filesize\ =.+?$/upload_max_filesize = 100M/" /etc/php5/fpm/php.ini
RUN sed -E -i "s/^display_errors\ .+?$/display_errors = On/" /etc/php5/fpm/php.ini
RUN sed -E -i "s/^error_reporting\ .+?$/error_reporting = E_ALL \& \~E_DEPRECATED \& \~E_STRICT \& \~E_NOTICE/" /etc/php5/fpm/php.ini

RUN mkdir /var/www
RUN chown -R www-data:www-data /var/www

WORKDIR /var/www

EXPOSE 9000

CMD ["php5-fpm", "-F"]
