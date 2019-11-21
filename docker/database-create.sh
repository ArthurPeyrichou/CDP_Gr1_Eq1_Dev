wait-for-it db3306
php bin/console doctrine:database:create
php bin/console doctrine:migration:migrate
