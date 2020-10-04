ARG FROM_IMAGE
ARG HTTPD_CRON
FROM ${FROM_IMAGE}

FROM ${HTTPD_CRON}
WORKDIR /home/ps-store/tests

RUN apk add --no-cache php-simplexml php-phar php-mbstring php php-openssl php-json php-curl php-dom
RUN sed -i 's#^include_path = "\(.*\)"$#include_path = "\1:/home/ps-store/bin"#g' /etc/php7/php.ini
RUN apk add --no-cache wget && \
	wget "https://phar.phpunit.de/phpunit-9.phar" --progress=dot:mega -O /usr/bin/phpunit && \
	chmod ugo+x /usr/bin/phpunit && \
	apk del wget
RUN mkdir -p /tmp/html /tmp/rss /usr/local/apache2/htdocs/caches

ENTRYPOINT [ "/usr/bin/phpunit", "-c", "/home/ps-store/tests/phpunit.xml" ]
CMD [ "./" ]

COPY --from=0 /home/ps-store /home/ps-store

RUN ln -s /home/ps-store/bin /home/ps-store/src
ADD settings_override.ini /home/ps-store/resources/
ADD . /home/ps-store/tests

