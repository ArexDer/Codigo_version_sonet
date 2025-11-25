# 📧 CONFIGURACIÓN SMTP NATIVA - FINAN MOTORS

## 🎯 OPCIÓN 1: CONFIGURACIÓN CON HOSTING

### Para hosting compartido (más fácil):
En `functions.php`, cambiar la línea 43 de:
```php
add_action('phpmailer_init', 'finanmotors_setup_phpmailer');
```

A:
```php
add_action('phpmailer_init', 'finanmotors_setup_hosting_mail');
```

## 🎯 OPCIÓN 2: CONFIGURACIÓN SMTP EXTERNA

### Paso 1: Configurar credenciales
En `functions.php`, líneas 47-51, cambiar:

```php
$phpmailer->Host = 'mail.finanmotors.com'; // Tu servidor SMTP
$phpmailer->Username = 'wordpress@finanmotors.com'; // Tu email
$phpmailer->Password = 'tu_password_real_aqui'; // ¡IMPORTANTE!
```

### Paso 2: Configurar puerto según proveedor

**Gmail/Google Workspace:**
```php
$phpmailer->Host = 'smtp.gmail.com';
$phpmailer->Port = 587;
$phpmailer->SMTPSecure = 'tls';
$phpmailer->Username = 'wordpress@finanmotors.com';
$phpmailer->Password = 'app_password_google'; // Password de aplicación
```

**Hosting con cPanel:**
```php
$phpmailer->Host = 'mail.finanmotors.com'; // o mail.tudominio.com
$phpmailer->Port = 587;
$phpmailer->SMTPSecure = 'tls';
$phpmailer->Username = 'wordpress@finanmotors.com';
$phpmailer->Password = 'password_del_correo';
```

**SendGrid:**
```php
$phpmailer->Host = 'smtp.sendgrid.net';
$phpmailer->Port = 587;
$phpmailer->SMTPSecure = 'tls';
$phpmailer->Username = 'apikey';
$phpmailer->Password = 'tu_api_key_sendgrid';
```

## 🔧 CONFIGURACIÓN RÁPIDA

### 1. Crear el correo wordpress@finanmotors.com
- Ir al panel de hosting (cPanel)
- Crear nueva cuenta de correo
- Usuario: `wordpress`
- Dominio: `@finanmotors.com`
- Password: elegir uno seguro

### 2. Probar configuración
Ir a: `tu-sitio.com/wp-json/pdf/v1/test-email`
Método POST con:
```json
{
  "email": "tu-email@test.com"
}
```

## ⚡ SOLUCIÓN RÁPIDA PARA PRODUCCIÓN

**Agregar al final de `wp-config.php`:**
```php
// Configuración SMTP Finan Motors
define('SMTP_HOST', 'mail.finanmotors.com');
define('SMTP_USER', 'wordpress@finanmotors.com');
define('SMTP_PASS', 'tu_password_aqui');
define('SMTP_PORT', 587);
```

**Luego en `functions.php`, reemplazar líneas 47-51:**
```php
$phpmailer->Host = defined('SMTP_HOST') ? SMTP_HOST : 'localhost';
$phpmailer->Username = defined('SMTP_USER') ? SMTP_USER : 'wordpress@finanmotors.com';
$phpmailer->Password = defined('SMTP_PASS') ? SMTP_PASS : '';
$phpmailer->Port = defined('SMTP_PORT') ? SMTP_PORT : 587;
```

## 🚨 IMPORTANTE

1. **Nunca dejar passwords visibles** en el código
2. **Usar HTTPS** siempre para formularios
3. **Probar primero** con emails de prueba
4. **Revisar logs** en caso de problemas

## ✅ VERIFICACIÓN FINAL

El sistema enviará correos:
- ✉️ **Empresa**: `contacto@finanmotors.com`
- ✉️ **Cliente**: Email que llena en el formulario  
- 📧 **Remitente**: `wordpress@finanmotors.com`

¡Listo para funcionar! 🎉