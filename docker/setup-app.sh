DB_HOST=$(echo $DATABASE_URL | cut -d '@' -f 2 | cut -d '/' -f 1)

wait-for-it $DB_HOST -t 0
php bin/console doctrine:database:create
php bin/console doctrine:migration:migrate
php bin/console cache:warmup
