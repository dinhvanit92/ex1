#!/bin/sh

if [ "$APP_ENV" = "local" ]
then
    exit 0;
fi

cd /var/www/ex1

cp .env.local .env

# composerの実行
export COMPOSER_ALLOW_SUPERUSER=1

if [ "$APP_ENV" = "develop" ]
then
  # dev 環境用 開発ツール有り
  php -d memory_limit=-1 /usr/bin/composer --no-interaction install
else
  # prd stg 環境用 開発ツール無し
  php -d memory_limit=-1 /usr/bin/composer --no-interaction install --no-dev
fi

# キャッシュクリア
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

chown -R www:www /var/www/

