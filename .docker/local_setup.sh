#!/bin/sh
cd /var/www/ex1

cp .env.local .env


# composerの実行
export COMPOSER_ALLOW_SUPERUSER=1

php -d memory_limit=-1 /usr/bin/composer --no-interaction install

# マイグレーション＆seederの実行
php artisan migrate
php artisan migrate:fresh --seed

# キャッシュクリア
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

chown -R www:www /var/www/

