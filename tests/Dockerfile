ARG FROM_IMAGE
FROM ${FROM_IMAGE}

WORKDIR /home/ps-store/tests

RUN apk add --no-cache php-simplexml php-phar php-mbstring
RUN apk add --no-cache wget && \
	wget "https://phar.phpunit.de/phpunit-8.5.phar" --progress=dot:mega -O /usr/bin/phpunit && \
	chmod ugo+x /usr/bin/phpunit && \
	apk del wget
RUN mkdir -p /tmp/html /tmp/rss

ENTRYPOINT [ "/usr/bin/phpunit", "-c", "/home/ps-store/tests/phpunit.xml" ]
CMD [ "./" ]

RUN ln -s /home/ps-store/bin /home/ps-store/src

ADD settings_override.ini /home/ps-store/resources/
ADD . /home/ps-store/tests

