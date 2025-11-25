# 🚨 ERROR PERSISTENTE - PLUGIN SMTP INTERFIERE

## ❌ **Problema identificado:**

Tu servidor tiene un **plugin de SMTP activo** (probablemente Gmail SMTP) que está **sobrescribiendo** nuestra configuración y forzando el uso de `wordpress@finanmotors.com` con credenciales de Gmail que fallan.

## 🔍 **Evidencia del problema:**

```javascript
headers: [
  'From: Finan Motors <wordpress@finanmotors.com>',  // ❌ AÚN SALE WORDPRESS
  'Reply-To: Finan Motors <wordpress@finanmotors.com>' // ❌ TODAVÍA WORDPRESS
]

mail_errors: [
  "error": {
    "code": 401,
    "message": "Request had invalid authentication credentials for Gmail"
  }
]
```

## ✅ **SOLUCIÓN IMPLEMENTADA - MÁS AGRESIVA:**

He implementado una configuración **más agresiva** que:

1. **✅ Se ejecuta antes que cualquier plugin** (`plugins_loaded` prioridad 1)
2. **✅ Remueve todas las acciones previas** de `phpmailer_init`
3. **✅ Fuerza filtros con prioridad 999**
4. **✅ DESACTIVA completamente SMTP**

## 🔧 **VERIFICACIÓN INMEDIATA:**

### **1. Revisar plugins activos:**
- Ir a WordPress Admin → Plugins
- Buscar plugins con nombres como:
  - "SMTP"
  - "Gmail"  
  - "Mail"
  - "WP Mail SMTP"
  - "Easy WP SMTP"

### **2. Desactivar temporalmente plugins de correo:**
```
- WP Mail SMTP
- Easy WP SMTP  
- Gmail SMTP
- Postman SMTP
- FluentSMTP
```

### **3. Probar inmediatamente después:**
- Usar `diagnostico-correos.html`
- Verificar que los headers muestren `marketing@finanmotors.com`

## 🎯 **CONFIGURACIÓN WP-CONFIG (ALTERNATIVA):**

Si el problema persiste, agregar al final de `wp-config.php`:

```php
// FORZAR configuración de correo Finan Motors
define('WPMS_ON', false);
define('WPMS_MAIL_FROM', 'marketing@finanmotors.com');
define('WPMS_MAIL_FROM_FORCE', true);
define('WPMS_MAIL_FROM_NAME', 'Finan Motors');
define('WPMS_MAIL_FROM_NAME_FORCE', true);

// Desactivar SMTP forzado
define('WPMS_SMTP_HOST', '');
define('WPMS_SMTP_AUTH', false);
```

## 🔍 **DIAGNÓSTICO PASO A PASO:**

### **1. Verificar logs:**
En `wp-content/uploads/finanmotors_pdf_log.txt` buscar:
```
[FinanMotors] Forced simple mail configuration applied
```

### **2. Si NO aparece el log:**
- Otro plugin está bloqueando nuestra configuración
- Necesitamos desactivar plugins de SMTP

### **3. Si SÍ aparece pero aún falla:**
- El servidor tiene SMTP forzado a nivel de hosting
- Contactar al proveedor de hosting

## 🆘 **SOLUCIÓN DEFINITIVA:**

### **Opción A - Desactivar plugins SMTP:**
1. Desactivar todos los plugins de correo
2. Probar el sistema
3. ✅ Debería funcionar inmediatamente

### **Opción B - Configurar plugin existente:**
Si necesitas mantener un plugin SMTP:
1. Configurarlo para usar `marketing@finanmotors.com`
2. Usar credenciales correctas de tu hosting
3. NO usar Gmail SMTP sin App Password

### **Opción C - Hosting forzado:**
Si el hosting fuerza SMTP:
1. Contactar soporte técnico
2. Pedir que permitan función `mail()` de PHP
3. O configurar SMTP del hosting correctamente

## 🎉 **RESULTADO ESPERADO:**

Después de desactivar plugins de correo:
- ✅ Headers mostrarán `marketing@finanmotors.com`
- ✅ No habrá errores 401 de Gmail
- ✅ Los correos se enviarán correctamente

---

**🔥 El problema es un conflicto de plugins - la solución es desactivar plugins SMTP o configurarlos correctamente.**