ARG BASE_IMAGE=dunglas/frankenphp:1-php8.4-bookworm

FROM ${BASE_IMAGE} AS php-libs
ARG UID=1000
ARG GID=1000
ENV UID=${UID} \
    GID=${GID}

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        apt-transport-https ca-certificates curl \
        libnss3-tools \
        supervisor \
        git \
        zip \
        unzip \
    && install-php-extensions \
        pcntl \
        apcu \
        gd \
        opcache \
        pdo_mysql \
        zip \
        curl \
        xml \
        mbstring \
        uploadprogress \
        redis \
        mongodb \
    && groupadd -g ${GID} apps \
    && useradd -u ${UID} -g ${GID} apps \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /etc/nginx/*.d /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && mkdir -p /apps/storage/logs \
    && chown -R apps:apps /apps

FROM composer:lts AS composer
RUN composer self-update

FROM php-libs
WORKDIR /apps
ENV PATH="$PATH:/apps" 
# available option local, production
ARG DOCKER_ENV=local

COPY config/fraken/${DOCKER_ENV}-Caddyfile /etc/caddy/Caddyfile

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY config/php/${DOCKER_ENV}-php.ini ${PHP_INI_DIR}/php.ini

COPY config/${DOCKER_ENV}-supervisord.conf /etc/supervisord.conf
COPY --chown=${UID}:${GID} web /apps
COPY --chown=${UID}:${GID} composer.* /apps

USER apps
# RUN frankenphp php-cli -c ${PHP_INI_DIR}/php.ini /usr/bin/composer install --no-cache --no-dev
RUN if [ "$DOCKER_ENV" = "production" ]; then \
    composer install --no-cache --no-dev; \
else \
    composer install --no-cache; \
fi
USER root


ENTRYPOINT []
CMD [ "supervisord", "-n" ]