<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../bd.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'tutor') {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT nombre FROM usuarios WHERE id = :usuario_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$nombre = $row ? $row['nombre'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Menú principal Tutor</title>
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
            <div class="d-flex align-items-center mb-4">
                <h1 class="h3 mb-0 me-3">
                    <i class="bi bi-person-badge text-primary me-2"></i>
                    Bienvenido/a, <?php echo htmlspecialchars($nombre); ?>
                </h1>
            </div>
            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="ver_hijos.php" class="card text-decoration-none h-100">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-people text-primary display-6 me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Mis hijos/as</h5>
                                <p class="card-text text-muted mb-0">Ver información de mis hijos/as asociados/as.</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="ver_calificaciones.php" class="card text-decoration-none h-100">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-journal-text text-success display-6 me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Calificaciones</h5>
                                <p class="card-text text-muted mb-0">Consultar notas y promedios de mis hijos/as.</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="ver_faltas.php" class="card text-decoration-none h-100">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-x-circle text-danger display-6 me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Faltas y tardanzas</h5>
                                <p class="card-text text-muted mb-0">Ver ausencias y tardanzas registradas.</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="ver_observaciones.php" class="card text-decoration-none h-100">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-card-text text-warning display-6 me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Observaciones</h5>
                                <p class="card-text text-muted mb-0">Ver observaciones de profesores y preceptores.</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="ver_comunicaciones.php" class="card text-decoration-none h-100">
                        <div class="card-body d-flex align-items-center">
                            <i class="bi bi-envelope text-info display-6 me-3"></i>
                            <div>
                                <h5 class="card-title mb-1">Comunicaciones</h5>
                                <p class="card-text text-muted mb-0">Ver y firmar comunicaciones recibidas.</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <a href="../logout.php" class="btn btn-outline-danger mt-5">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
            </a>
        </main>
    </div>
</div>
<?php include '../modo_oscuro.php'; ?>
</body>
</html>