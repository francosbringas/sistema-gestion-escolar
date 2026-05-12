<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../bd.php';

$error = '';
$success = '';

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

$sql_estudiantes = "SELECT e.id, u.nombre FROM estudiantes e JOIN usuarios u ON e.usuario_id = u.id ORDER BY u.nombre";
$stmt_estudiantes = $conn->prepare($sql_estudiantes);
$stmt_estudiantes->execute();
$estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id = $_POST['estudiante_id'] ?? '';
    $contenido = trim($_POST['contenido'] ?? '');

    if (!$estudiante_id || $contenido === '') {
        $error = 'Por favor, complete todos los campos.';
    } else {
        $sql_insert = "INSERT INTO comunicaciones (estudiante_id, contenido, fecha) VALUES (:estudiante_id, :contenido, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':contenido', $contenido);
        $stmt_insert->execute();
        $success = 'Comunicación registrada correctamente.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Registrar Comunicaciones</title>
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
                    Registrar Comunicación
                </h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" action="registrar_comunicaciones.php" class="card p-3 shadow">
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
                        <label for="contenido" class="form-label">Contenido</label>
                        <textarea id="contenido" name="contenido" class="form-control" rows="4" required></textarea>
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