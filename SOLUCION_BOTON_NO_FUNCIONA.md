# 🔧 SOLUCIÓN: Botón "Subir y Analizar" no funciona

## 🎯 PROBLEMA
El botón "Subir y Analizar" no responde al hacer clic.

## 🔍 PASOS DE DEPURACIÓN

### PASO 1: Abrir la Consola del Navegador

1. Ve a: http://localhost/Servicredit/contabilidad/importar_balanza
2. Presiona **F12** (o clic derecho → Inspeccionar)
3. Ve a la pestaña **"Console"**
4. Recarga la página (F5)

### PASO 2: Verificar qué se muestra en la consola

Deberías ver estos mensajes al cargar la página:

```
=== Sistema de Importación de Balanza ===
jQuery version: 3.x.x
Formulario encontrado: 1
```

#### ✅ **Si ves estos mensajes:**
- jQuery está cargado correctamente
- El formulario se detecta
- Continúa al PASO 3

#### ❌ **Si NO ves estos mensajes:**

**Problema A: jQuery no cargado**
```
ERROR: $ is not defined
```
**Solución:** Verifica que el layout/header.php incluya jQuery.

**Problema B: Formulario no encontrado**
```
Formulario encontrado: 0
```
**Solución:** Hay un problema con los IDs HTML.

### PASO 3: Hacer clic en "Subir y Analizar"

Después de seleccionar un archivo, haz clic en el botón y observa la consola.

#### ✅ **Si ves:**
```
Formulario enviado
Enviando archivo: ejemplo_balanza.csv
Período: 01/2026
Procesando archivo...
```

→ El formulario funciona, el problema está en el servidor. Ve al PASO 4.

#### ❌ **Si NO ves "Formulario enviado":**

**Problema:** El evento submit no se está capturando.

**Solución rápida:** Usa el archivo de prueba:
1. Abre: http://localhost/Servicredit/test_importar_balanza.html
2. Selecciona un archivo CSV
3. Haz clic en "Enviar Test"
4. Verifica el resultado

### PASO 4: Verificar respuesta del servidor

Si el formulario se envía pero no pasa nada:

#### En la consola, busca:
```
Respuesta recibida: {status: "success", data: {...}}
```

#### ✅ **Si ves la respuesta:**
- El servidor funciona
- El problema es en mostrarVistaPreviaStep2()

#### ❌ **Si ves error:**
```
Error AJAX: {...}
Response Text: ...
```

**Lee el Response Text**, puede contener:
- Error de PHP
- Error de permisos de archivo
- Error de base de datos

## 🛠️ SOLUCIONES COMUNES

### SOLUCIÓN 1: jQuery no cargado

Verifica en `application/views/layout/header.php`:

```php
<!-- Debe tener esto: -->
<script src="<?php echo base_url('public/plugins/jquery/jquery.min.js'); ?>"></script>
```

O desde CDN:
```html
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
```

### SOLUCIÓN 2: Permisos de upload

Verifica en `php.ini`:

```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
```

Reinicia Apache después de cambiar.

### SOLUCIÓN 3: Ruta incorrecta

Verifica que la URL sea correcta:

```javascript
// Debería ser:
url: '<?php echo site_url("contabilidad/procesar_balanza"); ?>'

// Si no funciona, prueba la URL completa:
url: 'http://localhost/Servicredit/contabilidad/procesar_balanza'
```

### SOLUCIÓN 4: CSRF Token (si está activado)

Si CodeIgniter tiene CSRF activado, agrega:

```javascript
const formData = new FormData(this);
formData.append('<?php echo $this->security->get_csrf_token_name(); ?>', 
                '<?php echo $this->security->get_csrf_hash(); ?>');
```

### SOLUCIÓN 5: Archivo muy grande

Si el archivo es muy grande (>2MB por defecto):

```bash
# En php.ini
upload_max_filesize = 50M
post_max_size = 50M
```

## 🧪 ARCHIVO DE PRUEBA

He creado un archivo de prueba simple: `test_importar_balanza.html`

**Para usarlo:**

1. Abre: http://localhost/Servicredit/test_importar_balanza.html
2. Selecciona el archivo: `temp/ejemplo_balanza_enero_2026.csv`
3. Haz clic en "Enviar Test"
4. Observa el resultado

**Si este archivo funciona:**
→ El problema está en el formulario principal, no en el servidor.

**Si este archivo NO funciona:**
→ El problema está en el servidor (controlador/modelo).

## 📋 CHECKLIST DE VERIFICACIÓN

- [ ] F12 → Console abierta
- [ ] Se ven los mensajes de consola al cargar
- [ ] jQuery está cargado (version 3.x)
- [ ] Formulario encontrado: 1
- [ ] Al hacer clic se ve "Formulario enviado"
- [ ] Se ve "Enviando archivo: [nombre]"
- [ ] Se ve "Procesando archivo..."
- [ ] Se recibe respuesta del servidor
- [ ] No hay errores en rojo en la consola

## 🚨 ERRORES COMUNES Y SOLUCIONES

### Error 1: "$ is not defined"

```
Uncaught ReferenceError: $ is not defined
```

**Causa:** jQuery no está cargado.

**Solución:**
```php
// En layout/header.php, antes del </head>
<script src="<?php echo base_url('public/plugins/jquery/jquery.min.js'); ?>"></script>
```

### Error 2: "toastr is not defined"

```
Uncaught ReferenceError: toastr is not defined
```

**Causa:** Librería toastr no está cargada (NO es crítico, usa alerts como fallback).

**Solución (opcional):**
```php
// En layout/header.php
<link rel="stylesheet" href="<?php echo base_url('public/plugins/toastr/toastr.min.css'); ?>">
<script src="<?php echo base_url('public/plugins/toastr/toastr.min.js'); ?>"></script>
```

### Error 3: "Formulario encontrado: 0"

**Causa:** El ID del formulario no coincide.

**Solución:** Verifica en la vista que el form tenga:
```html
<form id="uploadForm" ...>
```

### Error 4: 500 Internal Server Error

**Causa:** Error en PHP (controlador o modelo).

**Solución:**
1. Revisa `application/logs/log-YYYY-MM-DD.php`
2. Busca el último error PHP
3. Corrige el error según el mensaje

### Error 5: 404 Not Found

```
POST http://localhost/Servicredit/contabilidad/procesar_balanza 404
```

**Causa:** La ruta no existe.

**Solución:** Verifica que el método exista en el controlador:
```php
// En Contabilidad.php
public function procesar_balanza() {
    // ...
}
```

## 📞 SIGUIENTE PASO

**AHORA MISMO:**

1. Abre la página: http://localhost/Servicredit/contabilidad/importar_balanza
2. Presiona **F12**
3. Ve a **Console**
4. Selecciona un archivo
5. Haz clic en "Subir y Analizar"
6. **Copia TODO lo que aparezca en la consola** (texto rojo = errores)
7. Compárteme ese texto

Con esa información sabré exactamente qué está fallando.

## 🔍 COMANDOS ÚTILES PARA LA CONSOLA

Abre la consola (F12 → Console) y pega estos comandos:

```javascript
// Verificar jQuery
console.log('jQuery:', typeof $ !== 'undefined' ? 'OK' : 'FALTA');

// Verificar formulario
console.log('Formulario:', $('#uploadForm').length > 0 ? 'OK' : 'FALTA');

// Verificar toastr
console.log('Toastr:', typeof toastr !== 'undefined' ? 'OK' : 'FALTA');

// Forzar envío de prueba (después de seleccionar archivo)
$('#uploadForm').submit();
```

Esto te dirá qué está faltando.

---

**¿Qué ves en la consola?** Esa es la clave para resolver el problema.
