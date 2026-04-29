# S6. Actividad practica

## 1. Descripcion del proyecto
Nuestro proyecto es una plataforma web de gestion inmobiliaria llamada "Hogar Ideal Peru". Permite administrar propiedades, vendedores, usuarios y mensajes de contacto.

## 2. Tecnologia y motor de base de datos
- Motor: MySQL
- Lenguaje: SQL
- Acceso: PDO (prepared statements) desde PHP

## 3. Diseno correcto (normalizacion)
- Definimos tablas principales: usuarios, vendedores, propiedades y mensajes.
- La relacion es: propiedades.vendedor_id -> vendedores.id (FK).
- Esta separacion evita redundancia y facilita el mantenimiento.

## 4. Seguridad
- Usamos contrasenas cifradas con bcrypt.
- Implementamos consultas preparadas con PDO para evitar inyeccion SQL.
- Protegimos rutas con middleware de sesion.
- Proponemos usuario de aplicacion con permisos limitados (ver SQL comentado en database/bienes_raices.sql).
- El usuario administrador se crea con install.php (no viene insertado en el SQL).

## 5. Backups
- Incluimos un script de respaldo: scripts/backup_mysql.bat.
- Incluimos un script de restauracion: scripts/restore_mysql.bat.
- Recomendamos backups diarios o semanales segun el volumen.
- Los archivos se guardan en una carpeta backups fuera del repositorio (local o nube).

## 6. Optimizacion
- Agregamos indices en campos de busqueda frecuente:
  - propiedades: vendedor_id, tipo, activo, created_at.
  - mensajes: leido, created_at, email.
- Esto mejora la velocidad de listados y filtros.

## 7. Evidencias sugeridas (capturas)
- Estructura de tablas en MySQL (phpMyAdmin o CLI).
- Consulta de propiedades con JOIN a vendedores.
- Login y panel admin funcionando.
- Ejecucion de backup (archivo .sql generado).

## 8. Presentación (orden)
1. Objetivo del proyecto y problema que resuelve.
2. Modelo de datos (tablas y relaciones).
3. Seguridad aplicada.
4. Backups y plan de recuperacion.
5. Optimizacion (indices).
6. Demo breve del sistema.

## 9. Comandos utiles (MySQL)
```sql
-- Ver indices
SHOW INDEX FROM propiedades;

-- Mostrar relaciones
SHOW CREATE TABLE propiedades;
```
