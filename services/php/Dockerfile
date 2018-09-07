FROM ubuntu:18.04

ENV DEBIAN_FRONTEND noninteractive

COPY ./sources.list /etc/apt/sources.list
RUN apt-get update

RUN apt-get install software-properties-common language-pack-en-base vim git wget curl -y
RUN locale-gen en_US.UTF-8

ENV LC_ALL=en_US.UTF-8
ENV LANG=en_US.UTF-8

RUN add-apt-repository ppa:ondrej/php && \
    apt-get update

RUN apt-get install php7.2-common php7.2-fpm php7.2-gd php7.2-cli php7.2-curl php7.2-dev php7.2-json php7.2-mbstring php7.2-xml php7.2-zip php-redis gcc libevent-dev -y --fix-missing

RUN cd / && \
    php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer && \
    composer config -g repo.packagist composer https://packagist.phpcomposer.com

RUN sed -i "s/;date.timezone =.*/date.timezone = Europe\/Moscow/" /etc/php/7.2/fpm/php.ini
RUN sed -i "s/;date.timezone =.*/date.timezone = Europe\/Moscow/" /etc/php/7.2/cli/php.ini

COPY ./.bashrc /root/.bashrc

# clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /home/www/chatter

COPY ./fpm-pool-www.conf /etc/php/7.2/fpm/pool.d/www.conf
RUN mkdir /run/php/ -p

EXPOSE 9000 8081

CMD ["/usr/sbin/php-fpm7.2", "-F"]
