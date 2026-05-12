<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../bd.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'estudiante') {
    header("Location: ../login.php");
    exit();
}

$estudiante_id = $_SESSION['usuario_id'];

$sql = "SELECT m.nombre AS materia, c.calificacion 
        FROM calificaciones c 
        JOIN materias m ON c.materia_id = m.id 
        WHERE c.estudiante_id = :estudiante_id";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
$stmt->execute();
$calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <h2>
                    <i class="bi bi-journal-text text-primary me-2"></i> Calificaciones
                </h2>
                <?php if (count($calificaciones) === 0): ?>
                    <div class="alert alert-info mt-4">No hay calificaciones registradas.</div>
                <?php else: ?>
                    <div class="table-responsive mt-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Materia</th>
                                    <th>Calificación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($calificaciones as $fila): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($fila['materia']); ?></td>
                                        <td><?php echo htmlspecialchars($fila['calificacion']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <?php include '../modo_oscuro.php'; ?>
</body>
</html>