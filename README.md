# Software para Kiosco

Sistema de punto de venta (POS) para kioscos y pequeños negocios.

## Requisitos

- PHP 8.1+
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Extensiones PHP: BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, ZIP

## Instalación

1. **Clonar o descargar el proyecto**

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos en `.env`**
```
DB_HOST=tu_host
DB_DATABASE=nombre_bd
DB_USERNAME=usuario_bd
DB_PASSWORD=contraseña_bd
```

5. **Ejecutar migraciones y seeders**
```bash
php artisan migrate --seed
```

6. **Iniciar servidor**
```bash
php artisan serve
```

## Credenciales por defecto (después del seed)

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | admin@kiosco.com | password |
| Cajero | cajero@kiosco.com | password |

## Configuración WhatsApp

Agregar en `.env`:
```env
WHATSAPP_BUSINESS_TOKEN=tu_token_de_meta
WHATSAPP_PHONE_NUMBER_ID=tu_phone_id
```

## Despliegue en Hostinger

1. Subir archivos al public_html (excluyendo vendor y node_modules)
2. Instalar dependencias en servidor: `composer install --no-dev --optimize`
3. Configurar base de datos MySQL desde el panel de Hostinger
4. Ejecutar: `php artisan migrate --seed`
5. Asegurar que storage tenga permisos: `chmod -R 775 storage bootstrap/cache`

## Estructura de Carpetas

```
├── app/
│   ├── Http/Controllers/    # Controladores
│   ├── Models/              # Modelos Eloquent
│   ├── Notifications/       # Notificaciones
│   └── Services/            # Servicios (WhatsApp)
├── database/
│   ├── migrations/          # Migraciones BD
│   └── seeders/            # Datos de prueba
├── resources/views/         # Vistas Blade
├── routes/web.php          # Rutas
└── public/                 # Archivos públicos
```

## Módulos

- **Dashboard**: Estadísticas y alertas de stock
- **Productos**: CRUD completo con código de barras
- **Ventas**: Punto de venta con cálculo automático
- **Tickets**: Generación PDF y envío WhatsApp
- **Usuarios**: Gestión de roles (admin/cajero)
- **Cajas**: Control de caja y movimientos

## Licencia

Software para Kiosco - Todos los derechos reservados
