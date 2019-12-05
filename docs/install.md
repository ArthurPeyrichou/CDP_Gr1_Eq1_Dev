# Guide d'installation

L'installation de l'applicaiton nécessite la mise en place de éléments suivants :
* **PHP** avec le gestionnaire de packages **Composer**
* Une base de données **MySQL** ou **MariaDB**
* **Nodejs** avec **npm** pour le packaging des assets
* (optionnel mais fortement recommandé pour le déploiement en prodution)
 Un **serveur web** dédié pour faire le lien avec PHP, par exemple **Apache**

Heureusement, l'utilisation de **Docker** permet d'automatiser l'installation de tous ces éléments !

## Déploiement avec Docker

Pour déployer l'application via docker, il est nécessaire d'installer docker sur la machine sur laquelle déployer
 (se rapporter à la documentation de [docker](https://docs.docker.com/)).
 Ensuite, il suffit de lancer la commande suivante dans le dossier racine de l'application :

``
docker-compose up -d --build
``

Cette commande va créer deux conteneurs et les lancer :
* *db*, qui contient une base de données MariaDB servant à l'application.
* *web*, qui contient une installation de PHP + Apache ainsi que les fichiers de l'application

Une fois les conteneurs en route, l'application est accessible à l'adresse http://<ip_de_votre_machine>:80. Par exemple,
pour accéder à l'application depuis la machine qui l'héberge, il suffit d'aller sur le lien 
[http://127.0.0.1:80](http://127.0.0.1:80).
