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

$sql = "SELECT observacion, fecha FROM observaciones WHERE estudiante_id = :estudiante_id ORDER BY fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
$stmt->execute();
$observaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <h2>
                    <i class="bi bi-card-text text-warning me-2"></i> Observaciones
                </h2>
                <?php if (count($observaciones) === 0): ?>
                    <div class="alert alert-info mt-4">No hay observaciones registradas.</div>
                <?php else: ?>
                    <ul class="list-group mt-4">
                        <?php foreach ($observaciones as $obs): ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($obs['fecha']))); ?>:</strong>
                                <?php echo nl2br(htmlspecialchars($obs['observacion'])); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <?php include '../modo_oscuro.php'; ?>
</body>
</html>