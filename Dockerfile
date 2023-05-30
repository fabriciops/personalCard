FROM php:7.4-apache

# Copia os arquivos da aplicação para o container
COPY . /var/www/html

# Habilita o módulo do Apache para mod_rewrite (se necessário)
RUN a2enmod rewrite

# Instala as extensões PHP necessárias
RUN docker-php-ext-install pdo_mysql

# Define o diretório de trabalho
WORKDIR /var/www/html

# Configuração do Apache para permitir a reescrita de URLs
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Copia as configurações do PHP
COPY docker/php-config.ini /usr/local/etc/php/conf.d/app.ini

# Expõe a porta 80 para acesso externo
EXPOSE 80

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install zip pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install

CMD ["php", "-S", "0.0.0.0:8888", "-t", "/var/www/html/routes"]
