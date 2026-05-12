# Sistema de Gestión Escolar

Sistema web multirol para la gestión académica de un curso escolar. Desarrollado como proyecto escolar en 2025.

## ¿Qué hace?

Permite gestionar la actividad académica de un curso con cuatro roles diferenciados, cada uno con acceso solo a lo que le corresponde:

- **Estudiante** → ve sus calificaciones, faltas, observaciones y comunicaciones
- **Profesor** → registra calificaciones, observaciones y comunicaciones por materia
- **Preceptor** → registra faltas, observaciones y comunicaciones del curso
- **Tutor/Padre** → consulta calificaciones, faltas y comunicaciones de sus hijos

## Tecnologías

- **Back-end:** PHP con PDO
- **Base de datos:** MySQL (diseño relacional normalizado, relaciones N:M, integridad referencial)
- **Front-end:** Bootstrap 5, JavaScript, jQuery
- **Seguridad:** bcrypt para contraseñas (`password_hash` / `password_verify`), tokens de recuperación con expiración, control de acceso por rol en cada endpoint

## Estructura del proyecto

```
/
├── login.php                  # Autenticación principal
├── logout.php
├── panel_control.php          # Redirección según rol
├── recuperar_contraseña.php   # Recuperación por token con expiración
├── bd.php                     # Conexión PDO (ver configuración)
├── barra_lateral.php          # Componente de navegación compartido
├── modo_oscuro.php            # Preferencia de tema
├── generador.php              # Generador de usuarios (desarrollo)
├── estudiantes/
│   ├── menu_principal.php
│   ├── ver_calificaciones.php
│   ├── ver_faltas.php
│   ├── ver_observaciones.php
│   └── ver_comunicaciones.php
├── profesores/
│   ├── menu_principal.php
│   ├── gestionar_materias.php
│   ├── registrar_calificaciones.php
│   ├── registrar_observaciones.php
│   ├── registrar_comunicaciones.php
│   └── ver_comunicaciones.php
├── preceptores/
│   ├── menu_principal.php
│   ├── ver_estudiantes.php
│   ├── registrar_faltas.php
│   ├── registrar_observaciones.php
│   ├── registrar_comunicaciones.php
│   └── ver_comunicaciones.php
├── tutores/
│   ├── menu_principal.php
│   ├── ver_hijos.php
│   ├── ver_calificaciones.php
│   ├── ver_faltas.php
│   ├── ver_observaciones.php
│   └── ver_comunicaciones.php
├── css/
└── js/
```

## Configuración local

**Requisitos:** PHP 8+, MySQL/MariaDB, servidor local (XAMPP o similar)

1. Clonar el repositorio en la carpeta `htdocs` (XAMPP) o equivalente
2. Importar `gestion_escolar.sql` en phpMyAdmin o desde consola:
   ```bash
   mysql -u root -p < gestion_escolar.sql
   ```
3. Copiar `bd.php.example` como `bd.php` y completar las credenciales:
   ```php
   $host     = 'localhost';
   $dbname   = 'gestion_escolar';
   $username = 'tu_usuario';
   $password = 'tu_contraseña';
   ```
4. Acceder a `http://localhost/sistema-gestion-escolar/login.php`

## Características de seguridad

- Contraseñas almacenadas con bcrypt (nunca en texto plano)
- Recuperación de contraseña por token único con expiración temporal
- Cada página verifica la sesión y el rol antes de ejecutar cualquier lógica
- Consultas preparadas con PDO en toda la capa de base de datos (sin SQL concatenado)
- Salida de datos con `htmlspecialchars` para prevenir XSS

## Limitaciones conocidas

- No implementa protección CSRF en formularios POST
- Las credenciales de BD van en archivo de configuración local (no variables de entorno)
- Proyecto desarrollado en entorno escolar con XAMPP; no está preparado para despliegue en producción sin ajustes adicionales

## Autor

**Bringas Franco Sebastián**  
Técnico en Informática – E.E.T.P. N°478  
Cursando Ingeniería en Inteligencia Artificial – FICH-UNL  
[francosbringas@gmail.com](mailto:francosbringas@gmail.com)
