wait-for-it db:3306
php bin/console doctrine:database:create
php bin/console doctrine:migration:migrate
