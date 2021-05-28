FROM node:14.17.0

LABEL authors="Gwendoline Dossegger <gwendoline.dossegger@heig-vd.ch>, Cassandre Wojciechowski <cassandre.wojciechowski@heig-vd.ch>"

RUN apt-get update &&\
    apt-get install -y vim

COPY src /opt/app

CMD ["node", "/opt/app/index.js"]
