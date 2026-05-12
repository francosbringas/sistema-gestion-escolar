<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$usuario_rol = $_SESSION['usuario_rol'] ?? '';
?>

<aside class="sidebar bg-white shadow-sm p-3" style="min-height:100vh;">
    <div class="text-center mb-4">
        <img src="../logo.png" alt="Logo Escuela" class="school-logo mb-2" style="width:90px;">
        <h6 class="mb-0" style="font-size:1rem;">E.E.T.P. N°478 "Dr. Nicolás Avellaneda"</h6>
    </div>
    <nav class="nav flex-column mb-auto">
        <?php if ($usuario_rol === 'estudiante'): ?>
            <a href="menu_principal.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-house-door me-2"></i> Inicio
            </a>
            <a href="ver_calificaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-journal-text me-2"></i> Calificaciones
            </a>
            <a href="ver_observaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-card-text me-2"></i> Observaciones
            </a>
            <a href="ver_faltas.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-x-circle me-2"></i> Faltas
            </a>
            <a href="ver_comunicaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-envelope me-2"></i> Comunicaciones
            </a>
        <?php elseif ($usuario_rol === 'tutor'): ?>
            <a href="../tutores/menu_principal.php" class="nav-link d-flex align-items-center mb-2">
            <i class="bi bi-house-door me-2"></i> Inicio
            </a>
            <a href="../tutores/ver_hijos.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-people me-2"></i> Mis hijos/as
            </a>
            <a href="../tutores/ver_calificaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-journal-text me-2"></i> Calificaciones de hijos
            </a>
            <a href="../tutores/ver_faltas.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-x-circle me-2"></i> Faltas y tardanzas
            </a>
            <a href="../tutores/ver_observaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-card-text me-2"></i> Observaciones
            </a>
            <a href="../tutores/ver_comunicaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-envelope me-2"></i> Comunicaciones
            </a>
        <?php elseif ($usuario_rol === 'profesor'): ?>
            <a href="../profesores/menu_principal.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-house-door me-2"></i> Inicio
            </a>
            <a href="../profesores/registrar_calificaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-pencil-square me-2"></i> Registrar calificaciones
            </a>
            <a href="../profesores/registrar_observaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-card-text me-2"></i> Registrar observaciones
            </a>
            <a href="../profesores/gestionar_materias.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-book me-2"></i> Gestionar materias
            </a>
            <a href="../profesores/registrar_comunicaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-envelope me-2"></i> Registrar comunicaciones
            </a>
        <?php elseif ($usuario_rol === 'preceptor'): ?>
            <a href="../preceptores/menu_principal.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-house-door me-2"></i> Inicio
            </a>
            <a href="../preceptores/ver_estudiantes.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-people me-2"></i> Ver estudiantes
            </a>
            <a href="../preceptores/registrar_observaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-card-text me-2"></i> Registrar observaciones
            </a>
            <a href="../preceptores/registrar_faltas.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-x-circle me-2"></i> Registrar faltas
            </a>
            <a href="../preceptores/registrar_comunicaciones.php" class="nav-link d-flex align-items-center mb-2">
                <i class="bi bi-envelope me-2"></i> Registrar comunicaciones
            </a>
        <?php endif; ?>
    </nav>
    <div class="mt-auto pt-4">
        <a href="../logout.php" class="nav-link text-danger d-flex align-items-center">
            <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
        </a>
    </div>
</aside>