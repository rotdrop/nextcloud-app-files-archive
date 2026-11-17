# SPDX-License-Identifier: AGPL-3.0-or-later
#
# The official Nextcloud images provide only one PHP version per release.
# Reuse their server files and entrypoint with an explicitly selected PHP.
ARG NEXTCLOUD_VERSION=33
ARG PHP_VERSION=8.3
FROM nextcloud:${NEXTCLOUD_VERSION}-apache AS nextcloud
FROM php:${PHP_VERSION}-apache-trixie

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        bzip2 rsync libfreetype6-dev libjpeg-dev libpng-dev libwebp-dev \
        libicu-dev libpq-dev libzip-dev libgmp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j2 bcmath exif gd gmp intl pcntl pdo_mysql pdo_pgsql zip \
    && a2enmod headers rewrite \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL --retry 3 -o /tmp/apcu.tar.gz \
      https://codeload.github.com/krakjoe/apcu/tar.gz/refs/tags/v5.1.28 \
    && echo '0e60ba9d0fa4021c57a70c071f3d8f71de236275d084e560d5959fec4edd8c32  /tmp/apcu.tar.gz' | sha256sum -c - \
    && mkdir /tmp/apcu \
    && tar -xzf /tmp/apcu.tar.gz --strip-components=1 -C /tmp/apcu \
    && cd /tmp/apcu \
    && phpize && ./configure --enable-apcu && make -j2 && make install \
    && docker-php-ext-enable apcu \
    && echo 'apc.enable_cli=1' >> "$PHP_INI_DIR/conf.d/docker-php-ext-apcu.ini" \
    && cd / && rm -rf /tmp/apcu /tmp/apcu.tar.gz

ENV PHP_MEMORY_LIMIT=512M PHP_UPLOAD_LIMIT=512M
RUN { \
      echo 'memory_limit=${PHP_MEMORY_LIMIT}'; \
      echo 'upload_max_filesize=${PHP_UPLOAD_LIMIT}'; \
      echo 'post_max_size=${PHP_UPLOAD_LIMIT}'; \
    } > "$PHP_INI_DIR/conf.d/nextcloud.ini"

COPY --from=nextcloud /usr/src/nextcloud /usr/src/nextcloud
COPY --from=nextcloud /entrypoint.sh /upgrade.exclude /

VOLUME /var/www/html
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
