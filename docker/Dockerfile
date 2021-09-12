FROM phpswoole/swoole

COPY ./rootfilesystem/ /
RUN cd /var/src/swoole-load-tester && composer install && cd .
