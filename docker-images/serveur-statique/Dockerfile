FROM php:7.2-apache
LABEL authors="Gwendoline Dossegger <gwendoline.dossegger@heig-vd.ch>, Cassandre Wojciechowski <cassandre.wojciechowski@heig-vd.ch>"

RUN apt-get update &&\
    apt-get install -y vim

COPY content/ /var/www/html/
