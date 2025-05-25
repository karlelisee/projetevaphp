FROM php:8.2-apache

# Installer extensions
RUN docker-php-ext-install pdo pdo_mysql

# Supprimer l'ancien contenu par défaut
RUN rm -rf /var/www/html/*

# Copier les fichiers de ton projet dans Apache
COPY . /var/www/html/

# Forcer les bons droits
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Activer le .htaccess si nécessaire
RUN a2enmod rewrite

# Assurer que index.php est la page par défaut
RUN echo "<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/allow-override.conf \
    && a2enconf allow-override
