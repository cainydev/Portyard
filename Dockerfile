FROM dunglas/frankenphp

RUN install-php-extensions \
    pcntl \
    pdo_mysql \
    redis \
    zip \
    gd \
    intl \
    bcmath \
    opcache \
    exif \
    imagick \
    sodium \
    curl \
    mbstring \
    xml \
    dom \
    fileinfo \
    json \
    tokenizer \
    ctype \
    filter \
    hash \
    session \
    phar \
    openssl \
    posix \
    sockets \
    sysvmsg \
    sysvsem \
    sysvshm

COPY . /app
