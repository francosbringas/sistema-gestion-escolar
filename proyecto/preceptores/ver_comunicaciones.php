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

$sql_comunicaciones = "SELECT c.*, e.id as estudiante_id, u.nombre as estudiante_nombre
FROM comunicaciones c
JOIN estudiantes e ON c.estudiante_id = e.id
JOIN usuarios u ON e.usuario_id = u.id
WHERE c.emisor_tipo = 'preceptor' AND c.emisor_id = :preceptor_id
ORDER BY c.fecha DESC";
$stmt_comunicaciones = $conn->prepare($sql_comunicaciones);
$stmt_comunicaciones->bindParam(':preceptor_id', $preceptor_id, PDO::PARAM_INT);
$stmt_comunicaciones->execute();
$comunicaciones = $stmt_comunicaciones->fetchAll(PDO::FETCH_ASSOC);

function obtenerFirmas($conn, $comunicacion_id) {
    $sql = "SELECT t.id, u.nombre, cf.fecha_firma
        FROM comunicaciones_firmas cf
        JOIN tutores t ON cf.tutor_id = t.id
        JOIN usuarios u ON t.usuario_id = u.id
        WHERE cf.comunicacion_id = :comunicacion_id
        ORDER BY cf.fecha_firma";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':comunicacion_id', $comunicacion_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Comunicaciones enviadas</title>
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
                <i class="bi bi-envelope text-info me-2"></i>
                Comunicaciones Enviadas
            </h2>
            <?php if (count($comunicaciones) === 0): ?>
                <div class="alert alert-info">No has enviado comunicaciones.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Estudiante</th>
                            <th>Contenido</th>
                            <th>Firmas de tutores</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($comunicaciones as $com): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($com['fecha']); ?></td>
                                <td><?php echo htmlspecialchars($com['estudiante_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($com['contenido']); ?></td>
                                <td>
                                    <?php
                                    $firmas = obtenerFirmas($conn, $com['id']);
                                    if (count($firmas) == 0) {
                                        echo '<span class="text-danger">Sin firmas</span>';
                                    } else {
                                        foreach ($firmas as $firma) {
                                            echo htmlspecialchars($firma['nombre']) . ' (' . htmlspecialchars($firma['fecha_firma']) . ')<br>';
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <a href="menu_principal.php" class="btn btn-secondary w-auto mt-3">Volver al menú</a>
        </main>
    </div>
</div>
<?php include '../modo_oscuro.php'; ?>
</body>
</html>