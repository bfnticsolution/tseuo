FROM php:8.2-cli

# Installer les dépendances nécessaires pour mysqli
RUN apt-get update && \
    apt-get install -y libpng-dev libjpeg-dev libonig-dev libxml2-dev libzip-dev unzip zip \
    libmariadb-dev && \
    docker-php-ext-install mysqli

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier tous les fichiers du projet dans le conteneur
COPY . .

# Exposer le port utilisé par le serveur PHP intégré
EXPOSE 10000

# Lancer le serveur PHP à partir du bon dossier
CMD ["php", "-S", "0.0.0.0:10000", "-t", "/var/www/html"]
