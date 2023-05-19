FROM node:19.9-alpine AS front

WORKDIR /var/www

RUN mkdir /var/www/resources

COPY resources/ /var/www/resources/
COPY package*.json /var/www/
COPY webpack.config.js /var/www/

RUN npm ci
RUN npm run build

FROM php:8.2-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl git unzip netcat libxml2-dev libpq-dev libzip-dev && \
    pecl install apcu && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install -j$(nproc) zip opcache intl pdo_pgsql pgsql && \
    docker-php-ext-enable apcu pdo_pgsql sodium && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

RUN mkdir -p /var/www/public/dist
COPY --from=front /var/www/public/dist /var/www/public/dist

COPY docker/php-fpm/wait-for-it.sh /usr/bin/wait-for-it

RUN chmod +x /usr/bin/wait-for-it

ARG DB_HOST=db
ENV DB_HOST=$DB_HOST

CMD composer i -o; wait-for-it $DB_HOST:5432 -- bin/console --no-interaction doctrine:migrations:migrate ; php-fpm
