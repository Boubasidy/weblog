FROM php:8.2-apache

# Active mod_rewrite (pour URL propres)
RUN a2enmod rewrite

# Copie la config Apache personnalisée
COPY .docker/apache/default.conf /etc/apache2/sites-available/000-default.conf

# Installe extensions PHP nécessaires (mysqli, pdo_mysql)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Définit le dossier de travail
WORKDIR /var/www/html

# Copie le code PHP dans le conteneur
COPY public/ /var/www/html/

#EXPOSE 80