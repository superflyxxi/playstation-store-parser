ARG HTTPD_CRON
FROM ${HTTPD_CRON}
MAINTAINER SuperFlyXXI <superflyxxi@yahoo.com>

ENV WWW_DIR=/usr/local/apache2/htdocs

RUN apk add --no-cache bash sed php php-openssl php-json php-curl php-dom
RUN sed -i 's#^include_path = "\(.*\)"$#include_path = "\1:/home/ps-store/bin"#g' /etc/php7/php.ini

# Default settings
ENV PSSTORE_HTML_DIR=${WWW_DIR}/entries/ \
	PSSTORE_RSS_DIR=${WWW_DIR}/rss/ \
	PSSTORE_HOST_BASE_URL=http://localhost/entries/ \
	PSSTORE_PSNOW_CACHE_FILE=${WWW_DIR}/caches/psnow_cache.json \
	PSSTORE_METACRITIC_CACHE_FILE=${WWW_DIR}/caches/metacritic_cache.json

ADD docker/before_entrypoint.sh / 
ADD resources/ /home/ps-store/resources/
ADD www ${WWW_DIR}
RUN mkdir -p ${WWW_DIR}/logs ${WWW_DIR}/rss ${WWW_DIR}/entries ${WWW_DIR}/caches
ADD resources/init.rss.xml ${WWW_DIR}/rss/playstationStore.rss.xml
ADD resources/init.rss.xml ${WWW_DIR}/rss/playStationNow.rss.xml
ADD docker/crontab /etc/crontabs/root
ADD src/ /home/ps-store/bin/
