#!/bin/sh

# Aguardar o banco de dados estar pronto
echo "Aguardando o banco de dados..."
while ! nc -z db 5432; do
    sleep 0.1
done
echo "Banco de dados pronto!"

# Instalar dependências do Composer se necessário
if [ ! -d "vendor" ]; then
    echo "Instalando dependências do Composer..."
    composer install
fi

# Gerar chave da aplicação se necessário
if [ -z "$(grep '^APP_KEY=' .env)" ] || [ "$(grep '^APP_KEY=' .env | cut -d'=' -f2)" = "" ]; then
    echo "Gerando chave da aplicação..."
    php artisan key:generate
fi

# Executar migrações
echo "Executando migrações..."
php artisan migrate --force

# Limpar caches
echo "Limpando caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Iniciar o PHP-FPM
echo "Iniciando PHP-FPM..."
php-fpm
