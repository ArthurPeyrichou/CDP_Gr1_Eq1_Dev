wait-for-it db:3306 -t 0
php bin/console doctrine:database:create
php bin/console doctrine:migration:migrate
php bin/console cache:warmup
