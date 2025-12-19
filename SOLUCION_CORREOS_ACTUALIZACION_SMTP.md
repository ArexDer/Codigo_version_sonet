# 🔧 SOLUCIÓN: Correos no se envían (Actualización SMTP)

## ❌ PROBLEMA IDENTIFICADO

Tu configuración estaba usando `isMail()` que es equivalente a ejecutar el comando `mail()` de PHP sin configuración SMTP real. Esto hace que:

- ✅ `wp_mail()` devuelve `true` (falso positivo)
- ✅ Se muestra "Enviado correctamente" en el navegador
- ❌ Los correos NUNCA llegan porque no hay servidor SMTP configurado

```php
// ANTIGUO (no funciona):
$phpmailer->isMail(); 
$phpmailer->Host = '';
$phpmailer->SMTPAuth = false;
```

---

## ✅ SOLUCIÓN IMPLEMENTADA

He actualizado [functions.php](functions.php) líneas 38-65 para usar **SMTP real** en lugar de `isMail()`.

### Paso 1: Obtén tus credenciales SMTP

Necesitas saber:
- **Host SMTP**: `mail.finanmotors.com` (o el servidor de tu hosting)
- **Puerto**: `587` (TLS) o `465` (SSL)
- **Usuario**: `marketing@finanmotors.com` (tu email)
- **Contraseña**: La contraseña de tu cuenta de correo

**Dónde encontrar esto:**
- 📧 **cPanel del hosting**: Correo → Cuentas de Correo → Detalles
- 🌐 **Google Workspace**: Contraseña de aplicación (APP PASSWORD)
- 🏢 **Otro hosting**: Contactar al soporte técnico

### Paso 2: Actualiza las credenciales en functions.php

Abre [functions.php](functions.php) y ve a la línea **48** (aprox):

```php
$phpmailer->Username = 'marketing@finanmotors.com'; // ← Tu email SMTP
$phpmailer->Password = 'TU_PASSWORD_AQUI'; // ⚠️ ← INGRESAR CONTRASEÑA REAL
```

Reemplaza:
- `'marketing@finanmotors.com'` → Tu email SMTP (puede ser igual o diferente)
- `'TU_PASSWORD_AQUI'` → **Tu contraseña real** (sin comillas adicionales)

Ejemplo correcto:
```php
$phpmailer->Username = 'marketing@finanmotors.com';
$phpmailer->Password = 'Qjqtr35ZgR'; // ← Contraseña real
```

### Paso 3: Verifica el servidor SMTP (línea 44-46)

Si tu hosting usa un servidor diferente, actualiza:

```php
$phpmailer->Host = 'mail.tudominio.com'; // ← Cambiar si es necesario
$phpmailer->Port = 587; // 587 (TLS) o 465 (SSL)
$phpmailer->SMTPSecure = 'tls'; // 'tls' o 'ssl'
```

**Ejemplos para diferentes proveedores:**

#### Gmail / Google Workspace:
```php
$phpmailer->Host = 'smtp.gmail.com';
$phpmailer->Port = 587;
$phpmailer->SMTPSecure = 'tls';
$phpmailer->Username = 'tu_email@gmail.com';
$phpmailer->Password = 'tu_app_password'; // NO la contraseña normal, sino APP PASSWORD
```

#### Hosting con cPanel (GoDaddy, Bluehost, etc.):
```php
$phpmailer->Host = 'mail.tudominio.com'; // o mail.finanmotors.com
$phpmailer->Port = 587;
$phpmailer->SMTPSecure = 'tls';
$phpmailer->Username = 'marketing@finanmotors.com';
$phpmailer->Password = 'password_del_cpanel';
```

#### SendGrid:
```php
$phpmailer->Host = 'smtp.sendgrid.net';
$phpmailer->Port = 587;
$phpmailer->SMTPSecure = 'tls';
$phpmailer->Username = 'apikey';
$phpmailer->Password = 'SG.xxxxxxxxxxxx'; // Tu API key
```

---

## 🧪 PRUEBAS

Después de actualizar las credenciales:

### Test 1: Envía un test desde el formulario
1. Llena una cotización
2. Envía el PDF
3. **Revisa tu correo en 2-5 minutos**

Si llega → ✅ **¡PROBLEMA RESUELTO!**

Si no llega → Ve al **Test 2**

### Test 2: Habilita debug y revisa logs

Descomenta estas líneas en [functions.php](functions.php) línea 54-55:

```php
// Debug: descomenta para ver logs SMTP
$phpmailer->SMTPDebug = 2;
$phpmailer->Debugoutput = 'error_log';
```

Quedaría así:

```php
// Debug: descomenta para ver logs SMTP en wp-content/uploads/finanmotors_pdf_log.txt
$phpmailer->SMTPDebug = 2;
$phpmailer->Debugoutput = 'error_log';
```

Luego:
1. Intenta enviar otro correo
2. Revisa el log en: `/wp-content/uploads/finanmotors_pdf_log.txt`
3. Busca errores como:
   - `SMTP ERROR: Password not accepted` → Contraseña incorrecta
   - `Failed to connect to server` → Host/Puerto incorrecto
   - `Authentication unsuccessful` → Credenciales inválidas

---

## ⚠️ RESPUESTA A TU PREGUNTA

> "¿Mi cuenta de marketing@finanmotors.com no me pidió la clave dentro del PHP o no va?"

**Respuesta:**

✅ **SÍ NECESITA la contraseña en PHP**, ya que:
- El servidor WordPress debe autenticarse contra el servidor SMTP
- Sin credenciales válidas, el servidor SMTP rechaza el envío
- Aunque `wp_mail()` devuelva `true`, el servidor no autenticará

**Pero tienes que:**
1. Colocar la contraseña real en la línea 49 de [functions.php](functions.php)
2. Asegurarte de que sea la contraseña correcta de la cuenta `marketing@finanmotors.com`
3. Si usas Google Workspace, debe ser una "Contraseña de aplicación" (APP PASSWORD), no la contraseña normal

---

## 📋 CHECKLIST DE CONFIGURACIÓN

- [ ] Identifiqué el servidor SMTP correcto (Host)
- [ ] Sé el puerto correcto (587 o 465)
- [ ] Tengo la contraseña real de `marketing@finanmotors.com`
- [ ] Actualicé línea 44 con el Host correcto
- [ ] Actualicé línea 47 con el Puerto correcto
- [ ] Actualicé línea 48 con el Usuario correcto
- [ ] Actualicé línea 49 con la Contraseña real
- [ ] Guardé el archivo [functions.php](functions.php)
- [ ] Testé enviando una cotización
- [ ] ✅ Recibí el correo correctamente

---

## 🆘 SI SIGUE SIN FUNCIONAR

Contacta a tu hosting y solicita:

1. "¿Cuál es el servidor SMTP correcto para mi dominio?"
   - Respuesta esperada: `mail.finanmotors.com` o similar

2. "¿Cuál es el puerto SMTP recomendado?"
   - Respuesta esperada: `587` (TLS) o `465` (SSL)

3. "¿Cuál es la contraseña de la cuenta `marketing@finanmotors.com`?"
   - Si no la recuerdas, solicita reseteo de contraseña en cPanel

4. "¿Hay límites de envío de correos por hora?"
   - Algunos hostings limitan a 100-300 correos/hora

Con esa información, podrás completar la configuración en [functions.php](functions.php).

---

**Actualización realizada:** Diciembre 2025
**Archivo modificado:** [functions.php](functions.php) líneas 38-65
