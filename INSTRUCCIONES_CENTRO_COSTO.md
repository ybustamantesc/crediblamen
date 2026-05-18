# INSTRUCCIONES: AGREGAR CENTROS DE COSTO

## 1. Ejecutar el archivo SQL

Abrir phpMyAdmin o el cliente MySQL y ejecutar el archivo:
`sql/add_centro_costo.sql`

Este archivo creará:
- La tabla `tb_centro_costo` con los 5 centros de costo
- El campo `centro_costo_id` en la tabla `tb_journal`

## 2. Verificar en phpMyAdmin

Después de ejecutar el SQL, verificar que:
- La tabla `tb_centro_costo` existe con 5 registros
- La tabla `tb_journal` tiene el nuevo campo `centro_costo_id`

## 3. Usar la nueva funcionalidad

Al crear un nuevo asiento en:
http://localhost/Servicredit/contabilidad/diario

Ahora aparecerá un nuevo campo obligatorio "Centro de Costo" con las opciones:
- 001 - Gerencia
- 002 - Administración
- 003 - Finanzas
- 004 - Crédito
- 005 - Cobranza

## Cambios realizados en el código:

1. **Modelo**: `application/models/Centro_costo_model.php` (nuevo)
2. **Vista Modal**: `application/views/contabilidad/modal_add.php` (modificado)
3. **Controlador**: `application/controllers/Contabilidad.php` (modificado - métodos diario, modal_add, save_entry)
4. **SQL**: `sql/add_centro_costo.sql` (nuevo)
