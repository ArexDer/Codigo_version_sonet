# 🔧 SOLUCIÓN: Correos No Se Envían (wp_mail devuelve true pero no llegan)

## 📋 Problema Identificado

La consola muestra:
```
sent_company: true
sent_client: true
message: "Correo(s) enviado(s) correctamente."
```

**PERO** los correos NO llegan a los destinatarios. Esto indica que:
- WordPress cree que envió los correos correctamente
- El servidor NO está procesando realmente los correos
- Problema típico con `mail()` de PHP sin configuración SMTP

---

## ✅ Cambios Implementados

### 1. **Activación de SMTP** ✨
He cambiado la configuración de `mail()` simple a **SMTP** en [functions.php](functions.php):

```php
// ANTES (no funcionaba):
add_action('phpmailer_init', 'finan_force_simple_mail', 999);

// AHORA (con SMTP):
add_action('phpmailer_init', 'finan_setup_smtp_mail', 999);
```

### 2. **Depuración SMTP Activada** 🔍
Agregué logging detallado en la función `finan_setup_smtp_mail`:

```php
$phpmailer->SMTPDebug = 2; // Nivel 2: cliente + servidor
$phpmailer->Debugoutput = function($str, $level) {
    error_log("[FINAN SMTP] $str");
};
```

### 3. **Logs Mejorados** 📊
Ahora el sistema registra:
- Estado real de `wp_mail()` (TRUE/FALSE)
- Headers y attachments enviados
- Mensajes SMTP del servidor
- Errores específicos

---

## 🧪 Pasos para Probar

### Opción 1: Usar el Test HTML (RECOMENDADO)

1. **Subir archivo** `test-smtp-completo.html` a la raíz de tu sitio web

2. **Acceder** a: `https://finan.ec/test-smtp-completo.html`

3. **Enviar test** con tu correo: `diegorivas10101@gmail.com`

4. **Revisar**:
   - ✅ Si llega el correo → SMTP funcionando
   - ❌ Si no llega → Ver logs en la página

### Opción 2: Probar desde el Cotizador

1. Llenar una cotización completa
2. Generar PDF
3. Enviar correo
4. **Revisar logs del servidor** para ver mensajes SMTP

---

## 🔍 Verificar Logs SMTP

### Logs de WordPress
Los logs se guardan en:
```
/wp-content/uploads/finan_pdf_log.txt
```

Busca líneas como:
```
[FINAN] SMTP configuration applied - Host: mail.finan.ec, Port: 587
[FINAN SMTP] Connection: opening to mail.finan.ec:587
[FINAN SMTP] SMTP -> SUCCESS: Connected to mail.finan.ec
[FINAN SMTP] AUTH LOGIN succeeded
```

### Logs de PHP (error_log)
También revisa el error_log de PHP del servidor, donde aparecerán mensajes `[FINAN SMTP]`.

---

## ⚠️ Posibles Problemas y Soluciones

### Problema 1: Error de Autenticación SMTP

**Síntoma:**
```
[FINAN SMTP] SMTP ERROR: Password not accepted
```

**Solución:**
1. Verificar que las credenciales en [functions.php](functions.php) línea 115-116 sean correctas:
```php
$phpmailer->Username = 'marketing@finanmotors.com';
$phpmailer->Password = 'Qjqtr35ZgR';
```

2. Confirmar con el proveedor del hosting que:
   - La cuenta `marketing@finanmotors.com` existe
   - La contraseña es correcta
   - SMTP está habilitado en el servidor de correo

### Problema 2: Puerto Bloqueado

**Síntoma:**
```
[FINAN SMTP] SMTP ERROR: Failed to connect to server
```

**Solución:**
Cambiar el puerto en [functions.php](functions.php) línea 110:

```php
// Intentar con puerto 465 (SSL):
$phpmailer->Port = 465;
$phpmailer->SMTPSecure = 'ssl';

// O puerto 25 (sin cifrado):
$phpmailer->Port = 25;
$phpmailer->SMTPSecure = '';
```

### Problema 3: Firewall o Restricciones del Hosting

**Síntoma:**
```
[FINAN SMTP] Connection timeout
```

**Soluciones:**
1. Contactar al hosting para verificar:
   - ✅ Puertos SMTP abiertos (587, 465, 25)
   - ✅ Firewall permite conexiones salientes SMTP
   - ✅ No hay rate limiting para correos

2. Si el hosting no permite SMTP externo, usar Gmail SMTP:
```php
$phpmailer->Host = 'smtp.gmail.com';
$phpmailer->Port = 587;
$phpmailer->SMTPSecure = 'tls';
$phpmailer->Username = 'tu-cuenta@gmail.com';
$phpmailer->Password = 'contraseña-de-aplicación'; // Generar en Google
```

### Problema 4: SPF/DKIM/DMARC

**Síntoma:**
Los correos llegan a SPAM o son rechazados.

**Solución:**
Configurar registros DNS en el dominio `finan.ec`:

```dns
; SPF - Autorizar servidor de correo
finan.ec. IN TXT "v=spf1 mx a include:_spf.finan.ec ~all"

; DKIM - Firma digital
default._domainkey.finan.ec. IN TXT "v=DKIM1; k=rsa; p=CLAVE_PUBLICA_AQUI"

; DMARC - Política de autenticación
_dmarc.finan.ec. IN TXT "v=DMARC1; p=quarantine; rua=mailto:postmaster@finan.ec"
```

---

## 🎯 Configuración SMTP Alternativa (Gmail)

Si el servidor `mail.finan.ec` sigue fallando, usar Gmail como relay:

### 1. Crear contraseña de aplicación en Google

1. Ir a: https://myaccount.google.com/apppasswords
2. Generar contraseña para "WordPress"
3. Copiar la contraseña (16 caracteres)

### 2. Actualizar [functions.php](functions.php)

```php
function finan_setup_smtp_mail($phpmailer)
{
    $phpmailer->SMTPDebug = 2;
    $phpmailer->Debugoutput = function($str, $level) {
        error_log("[FINAN SMTP] $str");
    };

    $phpmailer->isSMTP();
    $phpmailer->Host = 'smtp.gmail.com';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 587;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Username = 'tu-cuenta@gmail.com';
    $phpmailer->Password = 'xxxx xxxx xxxx xxxx'; // Contraseña de aplicación
    $phpmailer->From = 'marketing@finanmotors.com';
    $phpmailer->FromName = 'FINAN';
    $phpmailer->CharSet = 'UTF-8';
    $phpmailer->Timeout = 30;
    
    return $phpmailer;
}
```

---

## 📞 Contacto con Hosting

Si nada funciona, contactar al hosting con estas preguntas:

### Checklist para el Soporte Técnico:

- [ ] ¿Tienen habilitado el envío de correos SMTP?
- [ ] ¿Qué puertos SMTP están disponibles? (25, 465, 587)
- [ ] ¿Necesito una IP dedicada para enviar correos?
- [ ] ¿Tienen configurado SPF/DKIM/DMARC para `finan.ec`?
- [ ] ¿Hay algún límite de correos por hora/día?
- [ ] ¿Puedo usar un relay SMTP externo como Gmail/SendGrid?
- [ ] ¿Los registros MX de `finan.ec` están configurados correctamente?

### Información a Proporcionar:

```
Dominio: finan.ec
Correo remitente: marketing@finanmotors.com
Servidor SMTP: mail.finan.ec
Puerto: 587
Cifrado: TLS
```

---

## 🚀 Siguiente Paso

1. **Subir** `test-smtp-completo.html` al servidor
2. **Acceder** a `https://finan.ec/test-smtp-completo.html`
3. **Enviar test** y revisar resultados
4. **Reportar** qué mensaje aparece en el test

---

## 📊 Monitoreo Continuo

Después de resolver, monitorear:

### 1. Logs del Sistema
```bash
# Ver últimos logs
tail -f /path/to/wp-content/uploads/finan_pdf_log.txt | grep SMTP
```

### 2. Test Periódico
Ejecutar test de correo semanalmente para detectar fallos temprano.

### 3. Alternativas Profesionales

Si el problema persiste, considerar servicios externos:

- **SendGrid** (100 correos/día gratis)
- **Mailgun** (1000 correos/mes gratis)
- **Amazon SES** ($0.10 por 1000 correos)
- **Postmark** (100 correos/mes gratis)

Estos servicios garantizan entrega y no dependen del hosting.

---

## ✅ Verificación Final

Cuando funcione correctamente, deberías ver:

### En los Logs:
```
[FINAN] SMTP configuration applied
[FINAN SMTP] Connection: opening to mail.finan.ec:587
[FINAN SMTP] SMTP -> SUCCESS: Connected
[FINAN SMTP] AUTH LOGIN succeeded
[FINAN SMTP] MAIL FROM: <marketing@finanmotors.com>
[FINAN SMTP] RCPT TO: <contacto@finanmotors.com>
[FINAN SMTP] DATA
[FINAN SMTP] Message sent!
[finan_pdf] Company email wp_mail returned: TRUE
[finan_pdf] Client email wp_mail returned: TRUE
```

### En tu Bandeja:
- ✅ Correo a la empresa (`contacto@finanmotors.com`)
- ✅ Correo al cliente (con PDF adjunto)
- ✅ Sin errores en SPAM

---

**Fecha:** 2025-12-19  
**Versión:** 2.0 - SMTP Activado con Depuración
