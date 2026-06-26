# HotelMS - Sistema de Gestión Hotelera

Sistema completo de gestión hotelera con panel administrador, sitio web público editable y pasarelas de pago integradas.

## Requisitos

- PHP 8.1 o superior
- MySQL 8.0 o superior
- Extensiones: `mysqli`, `curl`, `json`, `openssl`, `session`
- Apache con `mod_rewrite` y `mod_headers`

## Instalación

1. Clonar o copiar los archivos al servidor web

2. Configurar base de datos:
   - Crear base de datos MySQL: `hotelms`
   - Importar `_database/hotelms.sql`

3. Configurar credenciales:
   - Editar `env.php` con datos de conexión a la base de datos
   - Generar una clave de encriptación única: `bin2hex(random_bytes(32))` y asignarla a `APP_ENCRYPTION_KEY`

4. Crear directorio `logs/` con permisos de escritura

5. Acceder al sistema:
   - Sitio público: `https://tudominio.com/public/`
   - Panel admin: `https://tudominio.com/`
   - Usuario por defecto: `admin` / contraseña: la configurada durante la instalación

## Seguridad

- **CSRF**: activado en todos los formularios del admin
- **API keys**: almacenadas cifradas (AES-256-CBC) en la base de datos
- **XSS**: salidas sanitizadas con `htmlspecialchars()`
- **SQL Injection**: todas las consultas con variables usan prepared statements
- **Rate limiting**: 5 intentos de login antes de bloqueo de 5 minutos
- **Headers de seguridad**: X-Frame-Options, X-Content-Type-Options, Referrer-Policy, X-XSS-Protection
- **Sesiones**: regeneradas tras login exitoso
- **Archivos sensibles**: protegidos via .htaccess

## Pasarelas de Pago Compatibles

- Stripe (tarjetas de crédito/débito)
- PayPal
- MercadoPago (tarjetas, PSE, Nequi, Daviplata, OXXO, Efecty)
- Transferencia bancaria manual

## Idiomas

- Español (es)
- English (en)
- Português (pt)

Detección automática por navegador. Selector manual en todas las páginas.

## Licencia

Uso comercial permitido.
