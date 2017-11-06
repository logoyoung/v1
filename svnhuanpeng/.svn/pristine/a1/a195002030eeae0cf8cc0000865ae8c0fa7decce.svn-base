#!/bin/bash
nginx='/usr/local/openresty/nginx/sbin/nginx'
nginx_conf='/usr/local/openresty/nginx/conf/nginx.conf'

nginx_restart(){
    pid=$(ps -ef |grep nginx |grep -v grep|awk '{print $2}')
    if [[ $pid == '' ]]; then
        sudo $nginx -c $nginx_conf
        if [ $? -ne 0 ]; then
            echo 'start nginx error'
        fi
    else
        sudo $nginx -s reload
    fi
}

php_fpm_restart(){
    pid=$(ps -ef |grep php-fpm |grep -v grep|awk '{print $2}')
    if [[ $pid == '' ]]; then
        sudo /sbin/service php-fpm start
        if [ $? -ne 0 ]; then
            echo 'start php-fpm error'
        fi
    else
        sudo /sbin/service php-fpm restart
        if [ $? -ne 0 ]; then
            echo 'restart php-fpm error'
        fi

    fi
}

#php_fpm_restart
nginx_restart