FROM node:22.14.0-alpine
WORKDIR /app

COPY ./frontend ./

RUN npm install -g @angular/cli --no-cache

RUN npm install

CMD ng serve --host 0.0.0.0
