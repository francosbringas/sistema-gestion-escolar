<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../bd.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'preceptor') {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$sql_prec = "SELECT id FROM preceptores WHERE usuario_id = :usuario_id";
$stmt_prec = $conn->prepare($sql_prec);
$stmt_prec->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_prec->execute();
$row_prec = $stmt_prec->fetch(PDO::FETCH_ASSOC);

if (!$row_prec) {
    header("Location: ../logout.php");
    exit();
}
$preceptor_id = $row_prec['id'];

$sql_estudiantes = "SELECT e.id, u.nombre 
    FROM preceptores_estudiantes pe
    JOIN estudiantes e ON pe.estudiante_id = e.id
    JOIN usuarios u ON e.usuario_id = u.id
    WHERE pe.preceptor_id = :preceptor_id
    ORDER BY u.nombre";
$stmt_estudiantes = $conn->prepare($sql_estudiantes);
$stmt_estudiantes->bindParam(':preceptor_id', $preceptor_id, PDO::PARAM_INT);
$stmt_estudiantes->execute();
$estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Ver Estudiantes</title>
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
                <h2 class="mb-4"><i class="bi bi-people text-primary me-2"></i>Estudiantes a cargo</h2>
                <?php if (count($estudiantes) === 0): ?>
                    <div class="alert alert-info">No tienes estudiantes asignados.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes as $est): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($est['id']); ?></td>
                                        <td><?php echo htmlspecialchars($est['nombre']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="d-flex gap-2">
                       <a href="menu_principal.php" class="btn btn-secondary w-auto">Volver al menú</a>
                    </div>
            </main>
        </div>
    </div>
    <?php include '../modo_oscuro.php'; ?>
</body>
</html>