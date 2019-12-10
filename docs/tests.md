# Les tests

Il est possible de lancer plusieurs séries de tests sur l'application. Ces séries sont
* Les tests unitaires, qui testent les classes de l'application.
* Les tests d'intégration, qui testent l'interaction entre les éléments de l'application.
* Les tests end to end, qui testent le comportement de l'application dans un navigateur réel.

La procédure pour lancer chacun de ces tests est décrite dans ce document. Ils nécessitent de disposer d'une version de **PHP supérieure ou égale à 7.3** munie du gestionnaire de paquets **composer**.
Pour être en mesure de lancer les tests, il est nécessaire de télécharger toutes les dépendances de l'application à l'aide de *composer*. Pour ce faire :

```
composer install
```

## Tests unitaires

Les tests unitaires sont ceux nécessitant le moins de pré-requis. 

Il suffit de lancer la commande suivante :

```
php bin/phpunit --testsuite unit
```

## Tests d'intégration

Les prérequis pour les tests d'intégration sont les mêmes que pour les tests unitaires, à l'exception prêt qu'il faut également une base de données **MySQL**.
Pour pouvoir lancer les tests correctement, il faut indiquer l'URL de la base de données dans le fichier *.env.test*, sous la forme **DATABASE_URL=mysql://\[username\]:\[password\]@\[url\]:\[port\]/\[databaseName\]**.
Ensuite, il est nécessaire d'utiliser les commandes suivantes pour créer le schéma et charger les données de test :

```
php bin/console doctrine:database:create --env test
php bin/console doctrine:migration:migrate --env test
php bin/console doctrine:fixtures:load --env test
```

Puis, pour lancer les tests :

```
php bin/phpunit --testsuite integration
```

## Tests End to End

Afin de lancer les tests end to end, il est nécessaire d'avoir créé la base de données de test comme indiqué dans le partie sur les tests d'intégration. De plus, il est nécessaire de disposer de **NodeJS** et **npm**. Pour lancer les tests, il faut alors installer les dépendances Javascript et compiler les ressources de l'application :

```
npm install
npm run build-dev
```

Il faut ensuite lancer le serveur :

```
php -S 127.0.0.1:9543 -t public
```

Et enfin lancer les tests :

```
node_modules/.bin/mocha --timeout 15000 --recursive tests/EndToEnd
```
