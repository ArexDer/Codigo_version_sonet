# Configuración de Envío de PDFs por Correo Electrónico

## Estado Actual de la Implementación

### ✅ Completado:

1. **Modificación del archivo HTML/JavaScript:**
   - Se agregó la función `enviarPDFPorCorreo()` que convierte el PDF a base64 y lo envía al servidor
   - Se modificaron las 4 funciones de generación de PDF:
     - `generarPDF()` - Para planes vehiculares estándar
     - `generarPDFJustoTiempo()` - Para plan justo a tiempo
     - `generarPDFInmobiliario()` - Para cotizaciones inmobiliarias (ambas versiones)

2. **Endpoint PHP configurado:**
   - El archivo `functions.php` ya tiene el endpoint `/wp-json/pdf/v1/send` configurado
   - Maneja tanto envío por JSON como por multipart
   - Envía correos a dos destinatarios:
     - Cliente: correo ingresado por el usuario
     - Empresa: `contacto@finanmotors.com`

### ⚙️ Configuración Necesaria en WordPress:

Para que el envío de correos funcione correctamente, asegúrate de tener configurado lo siguiente en tu WordPress:

#### 1. Plugin de SMTP (Recomendado)
Instala y configura uno de estos plugins para mejorar la entrega de correos:
- **WP Mail SMTP** (más popular)
- **Easy WP SMTP**
- **Post SMTP**

#### 2. Configuración en wp-config.php (Opcional)
Si quieres configurar el SMTP directamente, agrega estas líneas a tu `wp-config.php`:

```php
// Configuración SMTP
define('SMTP_USER', 'tu-correo@finanmotors.com');
define('SMTP_PASS', 'tu-password');
define('SMTP_HOST', 'smtp.tu-proveedor.com');
define('SMTP_FROM', 'wordpress@finanmotors.com');
define('SMTP_NAME', 'Finan Motors');
define('SMTP_PORT', '587');
define('SMTP_SECURE', 'tls');
define('SMTP_AUTH', true);
```

#### 3. Configuración opcional para WhatsApp (Ya incluida)
El código PHP ya incluye soporte para WhatsApp Cloud API. Si quieres activarlo, define estas constantes:

```php
define('WHATSAPP_CLOUD_TOKEN', 'tu-token-de-whatsapp');
define('WHATSAPP_PHONE_ID', 'tu-phone-id');
```

### 🧪 Cómo Probar:

1. **Prueba Básica:**
   - Completa una cotización en el formulario
   - Asegúrate de que el correo electrónico sea válido
   - Acepta términos y condiciones
   - Haz clic en "COTIZAR"
   - Autoriza la verificación
   - Haz clic en "DESCARGAR PDF"

2. **Verificar los logs:**
   Los logs se guardan en: `wp-content/uploads/finanmotors_pdf_log.txt`

3. **Verificar correos:**
   - Revisa la bandeja del cliente
   - Revisa `contacto@finanmotors.com`
   - Revisa carpetas de spam

### 🔧 Troubleshooting:

#### Si no llegan los correos:

1. **Verificar logs:**
   ```
   wp-content/uploads/finanmotors_pdf_log.txt
   ```

2. **Verificar configuración SMTP:**
   - Instala un plugin SMTP
   - Envía un correo de prueba

3. **Verificar permisos:**
   ```php
   // En functions.php, cambiar la línea:
   'permission_callback' => '__return_true',
   // Por:
   'permission_callback' => function() { return current_user_can('read'); }
   ```

#### Si el PDF no se genera:

1. **Verificar librerías JavaScript:**
   - jsPDF debe estar cargado
   - html2canvas debe estar disponible

2. **Verificar consola del navegador:**
   - Buscar errores en F12 > Console

### 📧 Estructura de los correos:

**Para la empresa (`contacto@finanmotors.com`):**
- Asunto: "Nueva cotización registrada: [Nombre Cliente]"
- Contenido: Datos del cliente y resumen de la cotización
- Adjunto: PDF de la cotización

**Para el cliente:**
- Asunto: "Tu cotización en Finan Motors"
- Contenido: Mensaje personalizado y resumen
- Adjunto: PDF de la cotización

### 🚀 Funcionalidades Incluidas:

1. **Descarga local del PDF** (se mantiene)
2. **Envío automático por correo** (nuevo)
3. **Logs detallados** para debugging
4. **Soporte para diferentes tipos de cotización:**
   - Vehicular con entrada
   - Vehicular sin entrada
   - Vehicular justo a tiempo
   - Inmobiliaria con entrada
   - Inmobiliaria sin entrada
5. **Validación de datos**
6. **Manejo de errores**

### 📝 Notas Importantes:

- Los PDFs se guardan temporalmente en `wp-content/uploads/finanmotors_pdfs/`
- Los archivos no se eliminan automáticamente (para auditoría)
- El sistema funciona sin internet para la generación local
- El envío por correo requiere conexión a internet

### 🔄 Próximos Pasos:

1. Subir el archivo `codigonuevo_car_inmob.html` modificado a tu servidor
2. Verificar que `functions.php` esté en tu tema de WordPress
3. Configurar plugin SMTP
4. Realizar pruebas completas
5. Monitorear logs durante las primeras semanas

---

## Resumen de Archivos Modificados:

1. ✅ `codigonuevo_car_inmob.html` - Agregado envío por correo
2. ✅ `functions.php` - Endpoint ya existente (sin modificaciones)

El sistema está listo para usar. Solo necesitas configurar el SMTP en WordPress para garantizar la entrega de correos.