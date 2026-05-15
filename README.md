🇦🇷 [Español](#español) | 🇺🇸 [English](#english)

---

<a name="español"></a>
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
├── login.php                    # Autenticación principal
├── logout.php
├── panel_control.php            # Redirección según rol
├── recuperar_contraseña.php     # Recuperación por token con expiración
├── bd.php                       # Conexión PDO (ver configuración)
├── barra_lateral.php            # Componente de navegación compartido
├── modo_oscuro.php              # Preferencia de tema
├── generador.php                # Generador de usuarios (desarrollo)
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

---

<a name="english"></a>
# School Management System

Multi-role web system for academic management of a school class. Developed as a school project in 2025.

## What does it do?

Manages the academic activity of a class with four distinct roles, each with access only to what is relevant to them:

- **Student** → views their grades, absences, observations and communications
- **Teacher** → registers grades, observations and communications per subject
- **Homeroom teacher** → registers absences, observations and communications for the class
- **Guardian/Parent** → views grades, absences and communications for their children

## Technologies

- **Back-end:** PHP with PDO
- **Database:** MySQL (normalized relational design, N:M relationships, referential integrity)
- **Front-end:** Bootstrap 5, JavaScript, jQuery
- **Security:** bcrypt for passwords (`password_hash` / `password_verify`), expiring recovery tokens, role-based access control on every endpoint

## Project structure

```
/
├── login.php                    # Main authentication
├── logout.php
├── panel_control.php            # Role-based redirection
├── recuperar_contraseña.php     # Token-based password recovery with expiration
├── bd.php                       # PDO connection (see setup)
├── barra_lateral.php            # Shared navigation component
├── modo_oscuro.php              # Theme preference
├── generador.php                # User generator (development tool)
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

## Local setup

**Requirements:** PHP 8+, MySQL/MariaDB, local server (XAMPP or similar)

1. Clone the repository into the `htdocs` folder (XAMPP) or equivalent
2. Import `gestion_escolar.sql` in phpMyAdmin or from the console:
   ```bash
   mysql -u root -p < gestion_escolar.sql
   ```
3. Copy `bd.php.example` as `bd.php` and fill in your credentials:
   ```php
   $host     = 'localhost';
   $dbname   = 'gestion_escolar';
   $username = 'your_user';
   $password = 'your_password';
   ```
4. Open `http://localhost/sistema-gestion-escolar/login.php`

## Security features

- Passwords stored with bcrypt (never in plain text)
- Password recovery via unique token with expiration
- Every page verifies session and role before executing any logic
- PDO prepared statements across the entire data layer (no concatenated SQL)
- Output sanitized with `htmlspecialchars` to prevent XSS

## Known limitations

- No CSRF protection on POST forms
- DB credentials stored in a local config file (not environment variables)
- Project developed in a school environment with XAMPP; not ready for production deployment without additional adjustments

## Author

**Bringas Franco Sebastián**  
IT Technician – E.E.T.P. N°478  
B.Sc. in Artificial Intelligence Engineering – FICH-UNL  
[francosbringas@gmail.com](mailto:francosbringas@gmail.com)
