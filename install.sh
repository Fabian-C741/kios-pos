#!/bin/bash

echo "=========================================="
echo "  Instalación Software para Kiosco"
echo "=========================================="
echo ""

# Pedir datos de BD
read -p "Host de MySQL (ej: sql306.epizy.com): " DB_HOST
read -p "Nombre de Base de Datos: " DB_DATABASE
read -p "Usuario de MySQL: " DB_USERNAME
read -p "Contraseña de MySQL: " DB_PASSWORD
read -p "URL del sitio (ej: https://tudominio.com): " APP_URL

echo ""
echo "Configurando archivo .env..."
echo ""

# Crear archivo .env
cat > .env << EOF
APP_NAME="Software para Kiosco"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=$APP_URL

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=$DB_HOST
DB_PORT=3306
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=log

WHATSAPP_BUSINESS_TOKEN=
WHATSAPP_PHONE_NUMBER_ID=
EOF

echo "✓ Archivo .env creado"
echo ""

echo "Generando clave de aplicación..."
php artisan key:generate

echo ""
echo "Ejecutando migraciones..."
php artisan migrate --force

echo ""
echo "Cargando datos de prueba..."
php artisan db:seed --force

echo ""
echo "Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "=========================================="
echo "  ¡Instalación completada!"
echo "=========================================="
echo ""
echo "Accede a: $APP_URL"
echo ""
echo "Credenciales:"
echo "  Admin: admin@kiosco.com / password"
echo "  Cajero: cajero@kiosco.com / password"
echo ""
