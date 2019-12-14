FROM superflyxxi.dlinkddns.com:5000/superflyxxi/httpd-cron:latest
MAINTAINER SuperFlyXXI <superflyxxi@yahoo.com>

ENV WWW_DIR=/usr/local/apache2/htdocs

RUN apk add --no-cache php php-openssl php-json php-curl php-simplexml

ADD resources/ /home/ps-store/resources/
ADD www ${WWW_DIR}
ADD resources/init.rss.xml ${WWW_DIR}/playstationStore.rss.xml
ADD resources/init.rss.xml ${WWW_DIR}/playStationNow.rss.xml
ADD docker/crontab /etc/crontabs/root
ADD src/ /home/ps-store/bin/

