ARG FROM_IMAGE
FROM ${FROM_IMAGE}

WORKDIR /home/ps-store/tests

RUN apk add --no-cache php-simplexml php-phar findutils
RUN apk add --no-cache wget && \
	wget "https://phar.phpunit.de/phpunit-6.5.phar" --progress=dot:mega -O /usr/bin/phpunit && \
	apk del wget
RUN mkdir -p /tmp/html /tmp/rss
ENV FILES=*

ENTRYPOINT ["sh", "-c", "find ./ -name ${FILES}.php | sort | xargs -L1 php"]

RUN ln -s /home/ps-store/bin /home/ps-store/src

ADD settings_override.ini /home/ps-store/resources/
ADD . /home/ps-store/tests

