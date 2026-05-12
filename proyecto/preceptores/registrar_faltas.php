<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../bd.php';

$error = '';
$success = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id = $_POST['estudiante_id'] ?? '';
    $fecha = $_POST['fecha'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    $valor = $_POST['valor'] ?? '';
    $motivo = trim($_POST['motivo'] ?? '');

    if (!$estudiante_id || !$fecha || !$tipo || $valor === '') {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } elseif (!is_numeric($valor) || $valor < 0) {
        $error = 'El valor debe ser un número positivo.';
    } else {
        $sql_insert = "INSERT INTO faltas (estudiante_id, fecha, tipo, valor, motivo) VALUES (:estudiante_id, :fecha, :tipo, :valor, :motivo)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':fecha', $fecha);
        $stmt_insert->bindParam(':tipo', $tipo);
        $stmt_insert->bindParam(':valor', $valor);
        $stmt_insert->bindParam(':motivo', $motivo);
        $stmt_insert->execute();
        $success = 'Falta registrada correctamente.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Registrar Faltas</title>
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
                    <i class="bi bi-x-circle text-danger me-2"></i>
                    Registrar Faltas
                </h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" action="registrar_faltas.php" class="card p-3 shadow">
                    <div class="mb-3">
                        <label for="estudiante_id" class="form-label">Estudiante</label>
                        <select id="estudiante_id" name="estudiante_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione un estudiante</option>
                            <?php foreach ($estudiantes as $est): ?>
                                <option value="<?php echo $est['id']; ?>"><?php echo htmlspecialchars($est['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select id="tipo" name="tipo" class="form-select" required>
                            <option value="" disabled selected>Seleccione el tipo</option>
                            <option value="inasistencia">Inasistencia</option>
                            <option value="tardanza">Tardanza</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor</label>
                        <input type="number" step="0.01" min="0" id="valor" name="valor" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo (opcional)</label>
                        <textarea id="motivo" name="motivo" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                       <button type="submit" class="btn btn-primary w-auto">Guardar</button>
                       <a href="menu_principal.php" class="btn btn-secondary w-auto">Volver al menú</a>
                    </div>
                </form>
            </main>
        </div>
    </div>
    <?php include '../modo_oscuro.php'; ?>
</body>
</html>