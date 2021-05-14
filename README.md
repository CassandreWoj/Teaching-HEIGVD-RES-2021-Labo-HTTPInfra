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