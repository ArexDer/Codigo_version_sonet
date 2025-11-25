# 🚨 SOLUCIÓN ERROR 401 - SMTP GMAIL

## ❌ **Error encontrado:**
```
mail_errors: [
  "error": {
    "code": 401,
    "message": "Request had invalid authentication credentials"
  }
]
```

**El problema:** WordPress está intentando usar SMTP de Gmail sin las credenciales correctas.

## ✅ **SOLUCIONES IMPLEMENTADAS:**

### 1. **Cambio a configuración simple** (RECOMENDADO)
He forzado el uso de `mail()` de PHP en lugar de SMTP para evitar errores de autenticación.

### 2. **Headers corregidos**
Todos los headers ahora usan `marketing@finanmotors.com` correctamente.

## 🔧 **PASOS PARA VERIFICAR:**

### **Opción A: Usar configuración simple (Ya activada)**
```php
// En functions.php línea 275 - YA ESTÁ ASÍ:
add_action('phpmailer_init', 'finanmotors_setup_simple_mail');
```

✅ **Ventajas:**
- No requiere configuración SMTP
- Más compatible con hosting compartido
- Evita errores de autenticación

### **Opción B: Si necesitas SMTP (Para hostings que lo requieran)**
Si tu hosting NO soporta `mail()`, cambiar línea 275 a:
```php
add_action('phpmailer_init', 'finanmotors_setup_smtp_mail');
```

**Y configurar credenciales en líneas 328-329:**
```php
$phpmailer->Username = 'marketing@finanmotors.com';
$phpmailer->Password = 'password_real_aqui';
```

## 🎯 **TEST DE VERIFICACIÓN:**

1. **Abrir** `diagnostico-correos.html`
2. **Ejecutar** test básico
3. **Verificar** que NO aparezcan errores 401

## 📧 **CONFIGURACIÓN DE GMAIL (Si usas SMTP):**

Para usar Gmail SMTP necesitas:

1. **Crear App Password:**
   - Ir a Google Account Settings
   - Security → App passwords
   - Crear password para "Mail"

2. **Configuración Gmail:**
   ```php
   $phpmailer->Host = 'smtp.gmail.com';
   $phpmailer->Port = 587;
   $phpmailer->Username = 'marketing@finanmotors.com';
   $phpmailer->Password = 'app_password_de_16_caracteres';
   ```

## 🔍 **DIAGNÓSTICO DEL ERROR:**

### **¿Por qué ocurrió?**
1. **Headers mixtos**: Algunos usando `wordpress@`, otros `marketing@`
2. **SMTP sin autenticación**: Intentaba usar Gmail sin credenciales válidas
3. **Configuración híbrida**: Mezclaba configuración simple con SMTP

### **¿Cómo se solucionó?**
1. ✅ **Unificar a `marketing@finanmotors.com`** en todos los headers
2. ✅ **Forzar configuración simple** para evitar SMTP
3. ✅ **Eliminar dependencias de autenticación externa**

## ⚡ **RESULTADO ESPERADO:**

Después de estos cambios:
- ❌ **Antes**: Error 401, headers mixtos
- ✅ **Ahora**: Envío directo, sin errores de autenticación

## 🆘 **SI PERSISTE EL PROBLEMA:**

1. **Verificar** que no hay plugins de SMTP activos
2. **Revisar** wp-config.php por configuraciones SMTP forzadas
3. **Contactar** al hosting sobre soporte de función `mail()`

---

**🎉 La configuración está optimizada para funcionar inmediatamente sin requerir SMTP externo.**