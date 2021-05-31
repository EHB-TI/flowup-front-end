#!/bin/sh
php artisan rabbitmq:consume > loggs.txt & php-fpm