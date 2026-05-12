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

function obtenerCalificaciones($conn, $estudiante_id) {
    $sql = "SELECT m.nombre as materia, c.trimestre, c.calificacion
            FROM calificaciones c
            JOIN materias m ON c.materia_id = m.id
            WHERE c.estudiante_id = :estudiante_id
            ORDER BY m.nombre, FIELD(c.trimestre, '1', '2', '3', 'final')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Calificaciones</title>
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
            <h2 class="mb-4"><i class="bi bi-journal-text text-success me-2"></i>Calificaciones de mis hijos/as</h2>
            <?php if (count($hijos) === 0): ?>
                <div class="alert alert-info">No tienes hijos/as registrados/as en el sistema.</div>
            <?php else: ?>
                <?php foreach ($hijos as $hijo): ?>
                    <div class="card mb-4">
                        <div class="card-header fw-bold"><?php echo htmlspecialchars($hijo['nombre']); ?></div>
                        <div class="card-body">
                            <?php
                            $notas = obtenerCalificaciones($conn, $hijo['id']);
                            if (count($notas) === 0): ?>
                                <div class="alert alert-secondary">Sin calificaciones cargadas.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th>Materia</th>
                                            <th>Trimestre</th>
                                            <th>Calificación</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($notas as $nota): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($nota['materia']); ?></td>
                                                <td>
                                                    <?php
                                                    switch ($nota['trimestre']) {
                                                        case '1': echo '1°'; break;
                                                        case '2': echo '2°'; break;
                                                        case '3': echo '3°'; break;
                                                        case 'final': echo 'Final'; break;
                                                        default: echo htmlspecialchars($nota['trimestre']);
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($nota['calificacion']); ?></td>
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