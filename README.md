
APP_ENV=prod APP_DEBUG=0 php7 bin/console cache:clear

php7 bin/console doctrine:database:create
php7 bin/console doctrine:schema:create


