FROM php:8.2-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl git unzip netcat libxml2-dev libpq-dev libzip-dev && \
    pecl install apcu && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install -j$(nproc) zip opcache intl pdo_pgsql pgsql && \
    docker-php-ext-enable apcu pdo_pgsql sodium && \
    curl -fsSL https://deb.nodesource.com/setup_19.x | bash - &&\
    apt-get install -y nodejs &&\
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY wait-for-it.sh /usr/bin/wait-for-it

RUN chmod +x /usr/bin/wait-for-it

WORKDIR /var/www

ARG DB_HOST=db
ENV DB_HOST=$DB_HOST

CMD npm i && \
    npm run build ; \
    composer i -o ; \
    wait-for-it $DB_HOST:5432 -- bin/console --no-interaction doctrine:migrations:migrate ; php-fpm
