# 📧 INSTRUCCIONES FINALES - SISTEMA DE PDFs POR CORREO

## ✅ ¿Qué se ha completado?

1. **📄 HTML modificado** (`codigonuevo_car_inmob.html`)
   - Función `enviarPDFPorCorreo()` agregada
   - Integración en todos los botones de PDF
   - Sistema de notificaciones al usuario

2. **⚙️ Functions.php actualizado**
   - Endpoint REST API: `/wp-json/pdf/v1/send`
   - **SMTP nativo configurado** desde `wordpress@finanmotors.com`
   - Plantillas HTML profesionales para correos
   - Sistema de logs en `wp-content/uploads/finanmotors_pdf_log.txt`
   - Test endpoint: `/wp-json/pdf/v1/test-email`

## 🚀 PASOS PARA ACTIVAR EL SISTEMA

### 1. Configurar SMTP (OBLIGATORIO)

**OPCIÓN A - Hosting compartido (fácil):**
En `functions.php` línea 43, cambiar:
```php
add_action('phpmailer_init', 'finanmotors_setup_hosting_mail');
```

**OPCIÓN B - SMTP externo (recomendado):**
1. Seguir la **CONFIGURACION_SMTP_NATIVA.md**
2. Configurar credenciales en lines 47-51 de `functions.php`
3. Usar Gmail, hosting propio o SendGrid

### 2. Crear cuenta de correo
- Crear `wordpress@finanmotors.com` en el hosting
- Configurar password seguro
- Actualizar credenciales en `functions.php`

### 3. Probar el sistema
- Abrir `test-smtp.html` en el navegador
- O usar endpoint: `POST /wp-json/pdf/v1/test-email`

## 📊 FLUJO COMPLETO DEL SISTEMA

```
Usuario completa cotización
    ↓
Genera PDF con jsPDF
    ↓
Llama función enviarPDFPorCorreo()
    ↓
Envía al endpoint WordPress
    ↓ (SMTP desde wordpress@finanmotors.com)
WordPress envía 2 emails:
    • contacto@finanmotors.com (empresa) 
    • email del cliente
    ↓
Logs del resultado
```

## 🔍 DEBUGGING

### Test rápido desde navegador:
1. Abrir `test-smtp.html`
2. Ingresar email de prueba
3. Verificar que llegue el correo

### Si los correos NO llegan:
1. ✅ Verificar credenciales SMTP en `functions.php`
2. ✅ Revisar carpeta SPAM
3. ✅ Consultar logs: `wp-content/uploads/finanmotors_pdf_log.txt`
4. ✅ Usar el archivo `test-smtp.html`

### Código de test en consola del navegador:
```javascript
// Probar envío de PDF desde la página
enviarPDFPorCorreo('data:application/pdf;base64,JVBERi...', {
  nombre: 'Test',
  email: 'test@ejemplo.com',
  telefono: '123456789',
  tipoVehiculo: 'Automóvil',
  precio: 25000
});
```

## 🎯 CORREOS QUE SE ENVÍAN

### Para la empresa (contacto@finanmotors.com):
- ✉️ **Remitente**: `wordpress@finanmotors.com`
- ✉️ **Asunto**: "Nueva cotización registrada: [Nombre Cliente]"
- 📎 PDF adjunto con cotización completa
- 📋 Tabla con todos los datos del cliente
- ⏰ Fecha y hora del registro
- 🔔 Prioridad alta

### Para el cliente:
- ✉️ **Remitente**: `wordpress@finanmotors.com` 
- ✉️ **Asunto**: "Tu cotización en Finan Motors"
- 📎 PDF adjunto personalizado
- 🎨 Diseño profesional con gradientes
- 📞 Información de contacto y próximos pasos
- ✨ Mensaje de agradecimiento personalizado

## ⚠️ CONFIGURACIÓN CRÍTICA

**En `functions.php` líneas 47-51, actualizar:**
```php
$phpmailer->Host = 'mail.finanmotors.com'; // Tu servidor SMTP
$phpmailer->Username = 'wordpress@finanmotors.com'; 
$phpmailer->Password = 'TU_PASSWORD_REAL_AQUI'; // ¡CAMBIAR!
```

## 📞 SOPORTE Y VERIFICACIÓN

### Archivos de configuración creados:
- ✅ `CONFIGURACION_SMTP_NATIVA.md` - Guía completa
- ✅ `test-smtp.html` - Test visual del sistema
- ✅ `INSTRUCCIONES_FINALES.md` - Este archivo

### En caso de problemas:
1. Revisar logs de error de WordPress
2. Verificar que `wordpress@finanmotors.com` exista
3. Probar con `test-smtp.html`
4. Consultar `CONFIGURACION_SMTP_NATIVA.md`

---
**🎉 ¡Sistema SMTP nativo configurado y listo!**  
**Solo falta configurar las credenciales y probar.**