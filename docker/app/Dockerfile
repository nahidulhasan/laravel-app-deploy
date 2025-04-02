FROM docker.io/nahid35/base-php:7.4.29-3

LABEL maintainer="Nahidul Hasan<nahidul.cse@gmail.com>"

# Setup docker arguments.
ARG APPLICATION_TIMEZONE="Asia/Dhaka"
ARG BUILD_MODE="prod"
ARG HTTP_PROXY=""
ARG HTTPS_PROXY=""
ARG NO_PROXY="localhost,127.0.0.1"
ARG INSTALL_APT_PACKAGES=""
ARG INSTALL_PHP_EXTENSIONS=""

# Setup some enviornment variables.
ENV http_proxy="${HTTP_PROXY}" \
    https_proxy="${HTTPS_PROXY}" \
    no_proxy="${NO_PROXY}" \
    TZ="${APPLICATION_TIMEZONE}"

USER root

COPY ./docker/.commons/trust-ca/* /usr/local/share/ca-certificates/

RUN echo "--- Set Timezone ---" \
        && echo "${APPLICATION_TIMEZONE}" > /etc/timezone \
        && rm /etc/localtime \
        && dpkg-reconfigure -f noninteractive tzdata \
    && echo "-- Trust CA Certs --" \
        && update-ca-certificates \
    && echo "-- Install APT Dependencies --" \
        && apt update \
        && apt upgrade -y \
        && if [ ! -z "${INSTALL_APT_PACKAGES}" ]; then \
            apt install -y ${INSTALL_APT_PACKAGES} \
        ;fi \
    && echo "-- Install PHP Extensions --" \
        && if [ ! -z "${INSTALL_PHP_EXTENSIONS}" ]; then \
            install-php-extensions ${INSTALL_PHP_EXTENSIONS} \
        ;fi \
    && echo "--- Clean Up ---" \
        && apt-get clean -y \
        && apt-get autoremove -y \
        && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /var/www/html

ARG UID="1000"
ARG GID="1000"

RUN groupadd --gid ${GID} app \
    && useradd --uid ${UID} --create-home --system --comment "App User" --shell /bin/bash --gid app app \
    && chown -R app:app /var/www/html

COPY --chown=app:app ./composer.json ./composer.lock* /var/www/html/

#RUN export composerInstallArgs="--no-interaction --no-scripts --no-autoloader --prefer-dist" \
#    && if [ "${BUILD_MODE}" = "prod" ]; then \
#        export composerInstallArgs="${composerInstallArgs} --no-dev" \
#    ;fi \
#    && composer install $composerInstallArgs

COPY --chown=app:app ./ /var/www/html/
#
#RUN export composerInstallArgs="--no-interaction --no-scripts --no-autoloader --prefer-dist" \
#    && composer update $composerInstallArgs

RUN chown -R app:app /var/www/html \
    && export composerDumpAutoloadArgs="-o" \
    && if [ "${BUILD_MODE}" = "prod" ]; then \
        export composerDumpAutoloadArgs="$composerDumpAutoloadArgs --classmap-authoritative" \
    ;fi \
    && chown -R app:app /var/www/html ${COMPOSER_HOME} /etc/apache2/sites-available/ /run/apache2 /run/php /run/lock/apache2 /var/log/apache2 \
    && chmod -R ugo+w /var/www/html/storage/

# Copy certs
COPY ./docker/.commons/certs/* /etc/ssl/certs/
RUN chown -R app:app /etc/ssl/certs/


# Unset proxy ENVs
RUN unset http_proxy \
    && unset https_proxy \
    && unset no_proxy

USER app

CMD ["apache2-foreground"]
