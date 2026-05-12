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

function obtenerComunicaciones($conn, $estudiante_id, $tutor_id) {
    $sql = "SELECT c.*, 
        (SELECT fecha_firma FROM comunicaciones_firmas cf WHERE cf.comunicacion_id = c.id AND cf.tutor_id = :tutor_id) as fecha_firmada
    FROM comunicaciones c
    WHERE c.estudiante_id = :estudiante_id
    ORDER BY c.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
    $stmt->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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

// Firmar comunicación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['firmar_comunicacion_id'])) {
    $comunicacion_id = $_POST['firmar_comunicacion_id'];
    // Verifica que la comunicación exista y que el tutor no la haya firmado
    $sql_check = "SELECT id FROM comunicaciones_firmas WHERE comunicacion_id = :comunicacion_id AND tutor_id = :tutor_id";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bindParam(':comunicacion_id', $comunicacion_id, PDO::PARAM_INT);
    $stmt_check->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
    $stmt_check->execute();
    if ($stmt_check->rowCount() === 0) {
        $sql_insert = "INSERT INTO comunicaciones_firmas (comunicacion_id, tutor_id, fecha_firma) VALUES (:comunicacion_id, :tutor_id, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bindParam(':comunicacion_id', $comunicacion_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':tutor_id', $tutor_id, PDO::PARAM_INT);
        $stmt_insert->execute();
    }
    // redireccionar para evitar doble envío
    header("Location: ver_comunicaciones.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Comunicaciones</title>
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
            <h2 class="mb-4"><i class="bi bi-envelope text-info me-2"></i>Comunicaciones recibidas</h2>
            <?php if (count($hijos) === 0): ?>
                <div class="alert alert-info">No tienes hijos/as registrados/as en el sistema.</div>
            <?php else: ?>
                <?php foreach ($hijos as $hijo): ?>
                    <div class="card mb-4">
                        <div class="card-header fw-bold"><?php echo htmlspecialchars($hijo['nombre']); ?></div>
                        <div class="card-body">
                            <?php
                            $comunicaciones = obtenerComunicaciones($conn, $hijo['id'], $tutor_id);
                            if (count($comunicaciones) === 0): ?>
                                <div class="alert alert-secondary">Sin comunicaciones registradas.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Emisor</th>
                                            <th>Contenido</th>
                                            <th>Firma</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($comunicaciones as $com): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($com['fecha']); ?></td>
                                                <td>
                                                    <?php echo ucfirst($com['emisor_tipo']) . ' - ' . htmlspecialchars(obtenerEmisor($conn, $com['emisor_tipo'], $com['emisor_id'])); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($com['contenido']); ?></td>
                                                <td>
                                                    <?php if ($com['fecha_firmada']): ?>
                                                        <span class="badge bg-success">Firmada el <?php echo htmlspecialchars($com['fecha_firmada']); ?></span>
                                                    <?php else: ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="firmar_comunicacion_id" value="<?php echo $com['id']; ?>">
                                                            <button type="submit" class="btn btn-outline-primary btn-sm w-auto">Firmar</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
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