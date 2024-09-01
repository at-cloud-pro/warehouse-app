FROM ghcr.io/at-cloud-pro/caddy-php:4.0.0 AS app

ENV APP_VERSION="0.2.0"

RUN apk update \
&& apk add --no-cache msmtp libpng-dev \
&& docker-php-ext-configure gd \
&& docker-php-ext-install gd

COPY . /app

RUN composer install

RUN chmod -R a+w /app \
&& chmod -R a+x /app/bin/* \
&& chown -R www-data:www-data /app/var \
&& chmod -R a+w /app/var \
&& chmod -R a+w /app/vendor \
&& chmod -R a+w /app/public

USER www-data:www-data

FROM app AS development

ENV APP_ENV="dev"
ENTRYPOINT ["./docker/dev/entrypoint"]

FROM app AS ci

ENV APP_ENV="test"
ENTRYPOINT ["./docker/ci/entrypoint"]

FROM app AS production

ENV APP_ENV="prod"
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
ENTRYPOINT ["./docker/prod/entrypoint"]
