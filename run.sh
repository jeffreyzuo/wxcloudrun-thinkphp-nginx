#!/bin/sh

EXEC=/usr/bin/redis-server
CLIEXEC=/usr/bin/redis-cli
redis-server /etc/redis.conf &
# 后台启动
php-fpm -D
# 关闭后台启动，hold住进程
nginx -g 'daemon off;' &

cd /wxcloudrun-wxcomponent
./main
