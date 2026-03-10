# Build stage
FROM node:25-alpine AS build

ARG CONFIG="production"

WORKDIR /app

COPY frontend/package.json frontend/package-lock.json ./

RUN npm ci

COPY frontend/ .

RUN npm run build -- --configuration ${CONFIG}

#Host stage
FROM httpd:2.4-alpine

COPY docker/frontend/my-httpd.conf /usr/local/apache2/conf/httpd.conf

COPY --from=build /app/dist/frontend/browser /usr/local/apache2/htdocs/
