<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../bd.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'profesor') {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$sql_prof = "SELECT id FROM profesores WHERE usuario_id = :usuario_id";
$stmt_prof = $conn->prepare($sql_prof);
$stmt_prof->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_prof->execute();
$row_prof = $stmt_prof->fetch(PDO::FETCH_ASSOC);

if (!$row_prof) {
    header("Location: ../logout.php");
    exit();
}
$profesor_id = $row_prof['id'];

$sql_materias = "SELECT m.id, m.nombre FROM materias m
                 JOIN profesores_materias pm ON m.id = pm.materia_id
                 WHERE pm.profesor_id = :profesor_id";
$stmt = $conn->prepare($sql_materias);
$stmt->bindParam(':profesor_id', $profesor_id, PDO::PARAM_INT);
$stmt->execute();
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestionar Materias</title>
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
                <h2 class="mb-4">
                    <i class="bi bi-book text-success me-2"></i>
                    Materias a cargo
                </h2>
                <?php if (count($materias) === 0): ?>
                    <div class="alert alert-info">No tienes materias asignadas.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Materia</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($materias as $mat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($mat['id']); ?></td>
                                        <td><?php echo htmlspecialchars($mat['nombre']); ?></td>
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