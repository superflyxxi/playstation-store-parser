ARG FROM_IMAGE
FROM ${FROM_IMAGE}

WORKDIR /home/ps-store/tests

RUN apk add --no-cache php-simplexml findutils

ENV FILES=*

ENTRYPOINT ["sh", "-c", "find ./ -name ${FILES}.php | sort | xargs -L1 php"]

RUN ln -s /home/ps-store/bin /home/ps-store/src

ADD settings_override.ini /home/ps-store/resources/
ADD . /home/ps-store/tests

