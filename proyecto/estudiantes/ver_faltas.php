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

$sql = "SELECT fecha, tipo, valor, motivo FROM faltas WHERE estudiante_id = :estudiante_id ORDER BY fecha DESC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
$stmt->execute();
$faltas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Faltas (Inasistencias y Tardanzas)</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="stylesheet" href="../css/styles.css" />
    <style>
        .badge-inasistencia { background-color: #dc3545; color: white; }
        .badge-tardanza { background-color: #ffc107; color: black; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 p-0">
                <?php include '../barra_lateral.php'; ?>
            </div>
            <main class="col-md-9 col-lg-10 main-content p-4">
                <h2>
                    <i class="bi bi-x-circle text-danger me-2"></i> Faltas (Inasistencias y Tardanzas)
                </h2>
                <?php if (count($faltas) === 0): ?>
                    <div class="alert alert-info mt-4">No hay registros de faltas.</div>
                <?php else: ?>
                    <div class="table-responsive mt-4">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($faltas as $falta): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($falta['fecha']))); ?></td>
                                        <td>
                                            <?php if ($falta['tipo'] === 'inasistencia'): ?>
                                                <span class="badge badge-inasistencia">Inasistencia</span>
                                            <?php else: ?>
                                                <span class="badge badge-tardanza">Tardanza</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($falta['valor']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($falta['motivo'])); ?></td>
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