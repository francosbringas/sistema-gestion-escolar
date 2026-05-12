<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../bd.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'preceptor') {
    header("Location: ../login.php");
    exit();
}

$usuario_nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Inicio - Preceptor</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../css/styles.css" />
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 p-0">
                <?php include '../barra_lateral.php'; ?>
            </div>
            <main class="col-md-9 col-lg-10 main-content p-4">
                <h1 class="mb-4">¡Hola, <?php echo htmlspecialchars($usuario_nombre); ?>!</h1>
                <div class="row g-4">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-people display-4 text-primary mb-2"></i>
                                <h5 class="card-title">Ver Estudiantes</h5>
                                <p class="card-text">Consulta la lista de estudiantes a tu cargo.</p>
                                <a href="ver_estudiantes.php" class="btn btn-primary">Ver Estudiantes</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-card-text display-4 text-warning mb-2"></i>
                                <h5 class="card-title">Registrar Observaciones</h5>
                                <p class="card-text">Agrega observaciones sobre los estudiantes.</p>
                                <a href="registrar_observaciones.php" class="btn btn-primary">Registrar Observaciones</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-x-circle display-4 text-danger mb-2"></i>
                                <h5 class="card-title">Registrar Faltas</h5>
                                <p class="card-text">Registra inasistencias y tardanzas de los estudiantes.</p>
                                <a href="registrar_faltas.php" class="btn btn-primary">Registrar Faltas</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-envelope display-4 text-info mb-2"></i>
                                <h5 class="card-title">Registrar Comunicaciones</h5>
                                <p class="card-text">Envía notas de comunicación a los estudiantes.</p>
                                <a href="registrar_comunicaciones.php" class="btn btn-primary">Registrar Comunicaciones</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../modo_oscuro.php'; ?>
</body>
</html>