# RES - Laboratoire HTTP Infra

> Auteurs : Gwendoline Dössegger, Cassandre Wojciechowski
>
> Date : 30.05.2021

## Step 1 Serveur static HTTP
Pour cette partie, nous devions dockeriser un serveur HTTP. Pour cela, nous avons utilisé Apache httpd 7.2 avec l'image trouvée sur Docker hub.

Notre template HTML utilise le framework CSS Bootstrap que nous avons personnalisé : [template source](https://www.themezy.com/free-website-templates/156-pink-free-responsive-bootstrap-template)

Dans le Dockerfile, nous avons utilisé la commande `COPY` pour copier les fichiers constituant le site statique dans `/var/www/html/` du container. Cet emplacement est la racine du serveur Web d'Apache dans le container. 
[Lien vers le Dockerfile](https://github.com/CassandreWoj/Teaching-HEIGVD-RES-2021-Labo-HTTPInfra/blob/master/docker-images/serveur-statique/Dockerfile)
```shell
#Contenu du Dockerfile
FROM php:7.2-apache
LABEL authors="Gwendoline Dossegger <gwendoline.dossegger@heig-vd.ch>, Cassandre Wojciechowski <cassandre.wojciechowski@heig-vd.ch>"
COPY content/ /var/www/html/
```


/!\ Pour lancer le container, il faut impérativement se trouver dans le répertoire apache-php-image.
```sh
#Via les scripts :
./build-image.sh
./run-image.sh

#Via les commandes docker directement :
docker build --tag res/apache_php .
docker run -d -p 8080:80 res/apache_php 
```

Pour accéder au site statique, il existe deux moyens :
```shell
#1. On récupère l'adresse IP du container et on se connecte sur le port du container directement
# Pour trouver le nom du container : 
docker ps
# Pour trouver son adresse IP : 
docker inspect <name> | grep -i ipaddr
# Dans le navigateur
> 172.17.0.2:80


#2. On utilise le port mapping directement dans le navigateur de la machine hôte 
# avec l'adresse IP du Docker host et le port de notre machine
> 172.17.0.1:8080
```

Affichage du site statique via le navigateur
![Step1 - site statique](./images/step1.png)


## Step 2 Serveur dynamique HTTP avec express.js

Express.js est une infrastructure d'applications Web basées sur Node.js. 
Pour cette étape, nous créons un container Docker contenant une application dynamique, programmée avec Node.js, dont le rôle est de créer des adresses imaginaires en combinant des rues, des villes et des pays. Ces éléments ont été créé à l'aide du générateur Chancejs.

/!\ Pour lancer le container, il faut impérativement se trouver dans le répertoire express-dynamique. 
```shell
#Via les scripts :
./build-image.sh
./run-image.sh

#Via les commandes docker : 
docker build -t res/express_labo .
docker run -p 9090:3000 res/express_labo
```

Pour accéder à l'application dynamique, il existe deux moyens :
```shell
#1. On récupère l'adresse IP du container et on se connecte sur le port du container directement

# Pour trouver le nom du container : 
docker ps
# Pour trouver son adresse IP : 
docker inspect <name> | grep -i ipaddr
# Dans le navigateur
> 172.17.0.2:3000

 
#2. On utilise le port mapping directement dans le navigateur de la machine hôte 
# avec l'adresse IP du Docker host et le port de notre machine
> 172.17.0.1:9090
```
Nous pouvons aussi passer par le terminal avec la commande : `curl --location --request GET [172.17.0.2:3000](http://172.17.0.2:3000)`

Affichage du site statique via le navigateur
![Step2 - site statique](./images/step2.png)

## Step 3 Reverse proxy avec apache (configuration statique)

Pour cette étape, nous avons mis en place un reverse proxy avec apache dans un container. Le reverse proxy se trouve dans un container séparé, tout comme l'application dynamique et le site statique. 

Dans un premier temps, il faut s'assurer de lancer les containers suivants dans l'ordre car le reverse proxy est actuellement configuré de manière statique. 
```shell
# Run un container du step 1
docker build -t res/apache_php ../serveur-statique
docker run -d --name apache_static res/apache_php
docker inspect apache_static | grep -i ipaddress
#> ip : 172.17.0.2

# Run un container du step 2
docker build -t res/express_labo ../express-dynamique
docker run -d --name express_dynamic res/express_labo
docker inspect express_dynamic | grep -i ipaddress
#> ip : 172.17.0.3
```

Nous avons modifié les configurations du reverse proxy afin d'indiquer l'adresse sur laquelle l'utilisateur est redirigé. 
Ce fichier de configuration se trouve dans `conf/sites-available`. 
```shell
<VirtualHost *:80>
	ServerName res.labo.ch
	ProxyPass "/api/addresses/" "http://172.17.0.3:3000/"
	ProxyPassReverse "/api/addresses/" "http://172.17.0.3:3000/"

	ProxyPass "/" "http://172.17.0.2:80/"
	ProxyPassReverse "/" "http://172.17.0.2:80"
</VirtualHost>
```

Le Dockerfile associé est le suivant : 
```shell
FROM php:7.2-apache
LABEL authors="Gwendoline Dossegger <gwendoline.dossegger@heig-vd.ch>, Cassandre Wojciechowski <cassandre.wojciechowski@heig-vd.ch>"
COPY conf/ /etc/apache2
RUN apt-get update && apt-get install -y vim
RUN a2enmod proxy proxy_http
RUN a2ensite 000-* 001-*
```
Nous avons indiqué dedans de copier le contenu du répertoire `conf/` dans le répertoire `/etc/apache2` pour que le reverse proxy prenne la configuration mentionnée ci-dessus.
À l'aide de la première commande `RUN`, nous installons vim pour pouvoir modifier des fichiers directement dans le container en exécution.
Les deux commandes `RUN` suivantes permettent de configurer le proxy. 

Une fois les deux premiers containers lancés (avec le serveur statique et l'application dynamique), nous démarrons le container du reverse proxy.
```shell
# Lancement du proxy
docker build -t res/apache_rp .
docker run -p 8080:80 res/apache_rp
```

Il faut modifier le fichier `/etc/hosts` pour lui ajouter la correspondance `172.17.0.1     res.labo.ch`. 
Nous pourrons ensuite nous connecter sur l'adresse `http://172.17.0.1:8080` puis indiquer quelle ressource nous souhaitons accéder. 
Le site statique se trouve à la racine, tandis que l'application se trouve sur le chemin `/api/addresses/`. Ces chemins ont été définis dans les configurations expliquées ci-dessus.

Affichage du site statique via le navigateur (res.labo.ch:8080)
![Step3 - site statique](./images/step3_1.png)

Affichage du site statique via le navigateur (res.labo.ch:8080/api/addresses/)
![Step3 - site statique](./images/step3_2.png)
