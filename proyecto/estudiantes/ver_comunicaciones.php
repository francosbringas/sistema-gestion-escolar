<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../bd.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'estudiante') {
    header("Location: ../login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$sql_est = "SELECT id FROM estudiantes WHERE usuario_id = :usuario_id";
$stmt_est = $conn->prepare($sql_est);
$stmt_est->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt_est->execute();
$row_est = $stmt_est->fetch(PDO::FETCH_ASSOC);

if (!$row_est) {
    header("Location: ../logout.php");
    exit();
}
$estudiante_id = $row_est['id'];

$sql_comunicaciones = "SELECT c.*, 
    c.emisor_tipo, c.emisor_id
FROM comunicaciones c
WHERE c.estudiante_id = :estudiante_id
ORDER BY c.fecha DESC";
$stmt_comunicaciones = $conn->prepare($sql_comunicaciones);
$stmt_comunicaciones->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
$stmt_comunicaciones->execute();
$comunicaciones = $stmt_comunicaciones->fetchAll(PDO::FETCH_ASSOC);

function obtenerEmisor($conn, $tipo, $id) {
    if ($tipo == 'profesor') {
        $sql = "SELECT u.nombre FROM profesores p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?";
    } else {
        $sql = "SELECT u.nombre FROM preceptores p JOIN usuarios u ON p.usuario_id = u.id WHERE p.id = ?";
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Comunicaciones recibidas</title>
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
                Comunicaciones Recibidas
            </h2>
            <?php if (count($comunicaciones) === 0): ?>
                <div class="alert alert-info">No tienes comunicaciones.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Contenido</th>
                            <th>Emisor</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($comunicaciones as $com): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($com['fecha']); ?></td>
                                <td><?php echo htmlspecialchars($com['contenido']); ?></td>
                                <td>
                                    <?php echo ucfirst($com['emisor_tipo']) . ' - ' . htmlspecialchars(obtenerEmisor($conn, $com['emisor_tipo'], $com['emisor_id'])); ?>
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