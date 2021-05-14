# RES - Laboratoire HTTP Infra

> Auteurs : Gwendoline Dössegger, Cassandre Wojciechowski
>
> Date : 30.05.2021

## Step 1

## Step 2

docker build -t res/express_labo .

docker run res/express_labo

docker ps

docker inspect <name>

// trouver adresse IP du conteneur : 172.17.0.2

telnet <IP> 3000

Depuis l'extérieur : docker run -p 9090:3000 res/express_labo

curl --location --request GET '[172.17.0.2:3000](http://172.17.0.2:3000)'

## Step 3

1. 

docker run -d --name apache_static res/apache-php
docker inspect apache_static | grep -i ipaddress

2. 

docker run -d --name express_dynamic res/express_labo
docker inspect express_dynamic | grep -i ipaddress

3. 

docker build -t res/apache_rp .

docker run -p 8080:80 res/apache_rp

-> modification du fichier /etc/hosts pour dire 172.17.0.1 -> res.labo.ch

