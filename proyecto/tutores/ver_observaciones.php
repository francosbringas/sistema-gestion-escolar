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
$sql_tutor = "SELECT id FROM tutores WHERE usuario_id = :usuario_id";
$stmt_tutor = $conn->prepare($sql_tutor);
$stmt_tutor->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_tutor->execute();
$row_tutor = $stmt_tutor->fetch(PDO::FETCH_ASSOC);
if (!$row_tutor) {
    header("Location: ../logout.php");
    exit();
}
$tutor_id = $row_tutor['id'];

$sql_hijos = "SELECT e.id, u.nombre 
    FROM tutores_estudiantes te
    JOIN estudiantes e ON te.estudiante_id = e.id
    JOIN usuarios u ON e.usuario_id = u.id
    WHERE te.tutor_id = :tutor_id
    ORDER BY u.nombre";
$stmt_hijos = $conn->prepare($sql_hijos);
$stmt_hijos->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
$stmt_hijos->execute();
$hijos = $stmt_hijos->fetchAll(PDO::FETCH_ASSOC);

function obtenerObservaciones($conn, $estudiante_id) {
    $sql = "SELECT o.observacion, o.fecha, o.profesor_id, o.preceptor_id
            FROM observaciones o
            WHERE o.estudiante_id = :estudiante_id
            ORDER BY o.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerAutor($conn, $profesor_id, $preceptor_id) {
    if ($profesor_id) {
        $sql = "SELECT u.nombre FROM profesores p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$profesor_id]);
        return 'Profesor: ' . $stmt->fetchColumn();
    } elseif ($preceptor_id) {
        $sql = "SELECT u.nombre FROM preceptores p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$preceptor_id]);
        return 'Preceptor: ' . $stmt->fetchColumn();
    }
    return 'Desconocido';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Observaciones</title>
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
            <h2 class="mb-4"><i class="bi bi-card-text text-warning me-2"></i>Observaciones sobre mis hijos/as</h2>
            <?php if (count($hijos) === 0): ?>
                <div class="alert alert-info">No tienes hijos/as registrados/as en el sistema.</div>
            <?php else: ?>
                <?php foreach ($hijos as $hijo): ?>
                    <div class="card mb-4">
                        <div class="card-header fw-bold"><?php echo htmlspecialchars($hijo['nombre']); ?></div>
                        <div class="card-body">
                            <?php
                            $observaciones = obtenerObservaciones($conn, $hijo['id']);
                            if (count($observaciones) === 0): ?>
                                <div class="alert alert-secondary">Sin observaciones registradas.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Autor</th>
                                            <th>Observación</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($observaciones as $obs): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($obs['fecha']); ?></td>
                                                <td><?php echo htmlspecialchars(obtenerAutor($conn, $obs['profesor_id'], $obs['preceptor_id'])); ?></td>
                                                <td><?php echo htmlspecialchars($obs['observacion']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <a href="menu_principal.php" class="btn btn-secondary w-auto mt-3">Volver al menú</a>
        </main>
    </div>
</div>
<?php include '../modo_oscuro.php'; ?>
</body>
</html>