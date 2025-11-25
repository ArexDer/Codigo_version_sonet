# 📧 Guía Completa de Configuración SMTP para Finan Motors

## 🎯 Objetivo
Configurar el envío de correos electrónicos para que los PDFs de cotizaciones lleguen tanto al cliente como a `contacto@finanmotors.com`.

## 📋 Opciones de Configuración SMTP

### 🥇 OPCIÓN 1: WP Mail SMTP (Recomendado - Más Popular)

#### Instalación:
1. **En WordPress Admin:**
   - Ve a `Plugins` → `Añadir nuevo`
   - Busca "WP Mail SMTP"
   - Instala y activa "WP Mail SMTP by WPForms"

#### Configuración:
1. **Accede a la configuración:**
   - Ve a `Ajustes` → `WP Mail SMTP`

2. **Configuración General:**
   ```
   De Email: wordpress@finanmotors.com
   De Nombre: Finan Motors
   Mailer: SMTP
   ```

3. **Configuración SMTP:**
   ```
   Host SMTP: [Depende de tu proveedor - ver tabla abajo]
   Encriptación: TLS (recomendado) o SSL
   Puerto: [Depende del tipo de encriptación - ver tabla abajo]
   Autenticación: SÍ
   Usuario SMTP: [tu email completo]
   Contraseña SMTP: [tu contraseña de email]
   ```

### 🥈 OPCIÓN 2: Easy WP SMTP (Alternativa Simple)

#### Instalación:
1. `Plugins` → `Añadir nuevo` → Buscar "Easy WP SMTP"
2. Instalar y activar

#### Configuración:
1. Ve a `Ajustes` → `Easy WP SMTP`
2. Configura los mismos valores que en la Opción 1

### 🥉 OPCIÓN 3: Configuración Manual en wp-config.php

Si prefieres configurar directamente en código, agrega esto a tu `wp-config.php`:

```php
// Configuración SMTP personalizada para Finan Motors
define('SMTP_USER', 'wordpress@finanmotors.com');
define('SMTP_PASS', 'TU_CONTRASEÑA_AQUI');
define('SMTP_HOST', 'mail.finanmotors.com'); // Cambiar según tu proveedor
define('SMTP_FROM', 'wordpress@finanmotors.com');
define('SMTP_NAME', 'Finan Motors');
define('SMTP_PORT', '587');
define('SMTP_SECURE', 'tls');
define('SMTP_AUTH', true);
```

## 🌐 Configuración por Proveedores de Hosting/Email

### 🏢 **Hosting Compartido (cPanel)**
```
Host SMTP: mail.tudominio.com
Puerto TLS: 587
Puerto SSL: 465
Usuario: tu-email@finanmotors.com
```

### 📧 **Gmail/Google Workspace**
```
Host SMTP: smtp.gmail.com
Puerto: 587 (TLS) o 465 (SSL)
Usuario: tu-email@gmail.com
Contraseña: Contraseña de aplicación (no la normal)
```

**⚠️ Para Gmail necesitas:**
1. Activar verificación en 2 pasos
2. Generar "Contraseña de aplicación"
3. Usar esa contraseña en vez de la normal

### 🏢 **Microsoft 365/Outlook**
```
Host SMTP: smtp-mail.outlook.com
Puerto: 587
Usuario: tu-email@outlook.com
Encriptación: STARTTLS
```

### ☁️ **SendGrid (Recomendado para Empresas)**
```
Host SMTP: smtp.sendgrid.net
Puerto: 587
Usuario: apikey
Contraseña: Tu API Key de SendGrid
```

### ☁️ **Mailgun**
```
Host SMTP: smtp.mailgun.org
Puerto: 587
Usuario: Tu usuario de Mailgun
Contraseña: Tu contraseña de Mailgun
```

## 🔧 Configuración Paso a Paso - WP Mail SMTP

### Paso 1: Instalación
1. Login a tu WordPress Admin
2. `Plugins` → `Añadir nuevo`
3. Buscar "WP Mail SMTP"
4. Instalar el plugin de **WPForms**
5. Activar el plugin

### Paso 2: Configuración Básica
1. Ve a `WP Mail SMTP` → `Ajustes`
2. **Email del Remitente:**
   ```
   Email: wordpress@finanmotors.com
   Nombre: Finan Motors
   ```
3. **Seleccionar Mailer:** `SMTP`

### Paso 3: Configuración SMTP
```
Host SMTP: [Según tu proveedor]
Encriptación: TLS
Puerto SMTP: 587
Autenticación: Activado
Usuario SMTP: wordpress@finanmotors.com
Contraseña SMTP: [Tu contraseña]
```

### Paso 4: Configuración Avanzada
1. **En la pestaña "Avanzado":**
   ```
   Return Path: Marcado
   Auto TLS: Activado
   ```

### Paso 5: Prueba de Envío
1. Ve a `WP Mail SMTP` → `Email Test`
2. **Configurar prueba:**
   ```
   Para: tu-email-personal@gmail.com
   Asunto: Prueba SMTP Finan Motors
   Mensaje: Test de configuración SMTP
   ```
3. Hacer clic en "Enviar Email"

## 🚨 Resolución de Problemas Comunes

### ❌ **"Could not authenticate"**
**Solución:**
- Verifica usuario y contraseña
- Si es Gmail, usa "Contraseña de aplicación"
- Verifica que el email existe

### ❌ **"Connection timed out"**
**Solución:**
- Verifica el host SMTP
- Prueba puerto 465 (SSL) en lugar de 587 (TLS)
- Contacta a tu hosting sobre restricciones de puerto

### ❌ **"SSL connection failed"**
**Solución:**
- Cambia de TLS a SSL o viceversa
- Verifica que el puerto coincida con el tipo de encriptación

### ❌ **Correos van a SPAM**
**Solución:**
- Configura registros SPF/DKIM en tu dominio
- Usa un email del mismo dominio como remitente
- Evita palabras spam en asunto/contenido

## 🧪 Testing Completo del Sistema

### Prueba 1: Email Test del Plugin
```
1. WP Mail SMTP → Email Test
2. Enviar a tu email personal
3. Verificar que llegue correctamente
```

### Prueba 2: Prueba del Sistema de Cotizaciones
```
1. Ir a tu cotizador
2. Llenar formulario completo
3. Generar PDF
4. Verificar que lleguen 2 emails:
   - Uno a tu email personal
   - Uno a contacto@finanmotors.com
```

### Prueba 3: Verificar Logs
```
Archivo: wp-content/uploads/finanmotors_pdf_log.txt
Buscar: "Company email sent: yes" y "Client email sent: yes"
```

## 📧 Configuración Específica Recomendada para Finan Motors

### Opción A: Usar el hosting actual
```
Host SMTP: mail.finanmotors.com
Puerto: 587 (TLS)
Usuario: wordpress@finanmotors.com
De: wordpress@finanmotors.com
Nombre: Finan Motors
```

### Opción B: SendGrid (Profesional)
```
1. Crear cuenta en SendGrid
2. Verificar dominio finanmotors.com
3. Generar API Key
4. Configurar:
   Host: smtp.sendgrid.net
   Puerto: 587
   Usuario: apikey
   Contraseña: [Tu API Key]
```

## 🔒 Seguridad y Mejores Prácticas

### ✅ **Recomendaciones:**
- Crear email específico: `wordpress@finanmotors.com`
- Usar contraseñas fuertes
- Activar autenticación 2FA cuando sea posible
- Monitorear logs regularmente

### ✅ **Configuración DNS (Opcional pero Recomendado):**
```
Tipo: SPF
Nombre: @
Valor: v=spf1 include:_spf.google.com ~all

Tipo: DMARC  
Nombre: _dmarc
Valor: v=DMARC1; p=none; rua=mailto:admin@finanmotors.com
```

## 📞 Próximos Pasos

1. **Elegir opción SMTP** (Recomiendo WP Mail SMTP)
2. **Instalar y configurar plugin**
3. **Realizar pruebas**
4. **Verificar funcionamiento completo**
5. **Configurar monitoreo** (revisar logs semanalmente)

## 🆘 Soporte

Si tienes problemas:
1. **Verificar logs:** `wp-content/uploads/finanmotors_pdf_log.txt`
2. **Probar email test** del plugin
3. **Contactar hosting** para verificar restricciones SMTP
4. **Verificar configuración** paso a paso

---

**📧 ¿Tienes preguntas sobre algún paso específico?**