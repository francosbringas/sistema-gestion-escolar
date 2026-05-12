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

$sql_materias = "SELECT m.id, m.nombre FROM materias m
                 JOIN profesores_materias pm ON m.id = pm.materia_id
                 WHERE pm.profesor_id = :profesor_id";
$stmt = $conn->prepare($sql_materias);
$stmt->bindParam(':profesor_id', $profesor_id, PDO::PARAM_INT);
$stmt->execute();
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

function calcularNotaFinal($conn, $estudiante_id, $materia_id) {
    $sql = "SELECT calificacion FROM calificaciones 
            WHERE estudiante_id = :estudiante_id AND materia_id = :materia_id AND trimestre IN ('1','2','3')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
    $stmt->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
    $stmt->execute();
    $notas = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($notas) == 3) {
        $final = round(array_sum($notas) / 3, 2);
        $sql_check = "SELECT id FROM calificaciones WHERE estudiante_id = :estudiante_id AND materia_id = :materia_id AND trimestre = 'final'";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
        $stmt_check->execute();
        if ($stmt_check->rowCount() > 0) {
            $sql_update = "UPDATE calificaciones SET calificacion = :calificacion WHERE estudiante_id = :estudiante_id AND materia_id = :materia_id AND trimestre = 'final'";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':calificacion', $final);
            $stmt_update->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
            $stmt_update->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
            $stmt_update->execute();
        } else {
            $sql_insert = "INSERT INTO calificaciones (estudiante_id, materia_id, calificacion, trimestre) VALUES (:estudiante_id, :materia_id, :calificacion, 'final')";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':calificacion', $final);
            $stmt_insert->execute();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estudiante_id = $_POST['estudiante_id'] ?? '';
    $materia_id = $_POST['materia_id'] ?? '';
    $calificacion = $_POST['calificacion'] ?? '';
    $trimestre = $_POST['trimestre'] ?? '';

    if (!$estudiante_id || !$materia_id || $calificacion === '' || !$trimestre) {
        $error = 'Por favor, complete todos los campos.';
    } elseif (!is_numeric($calificacion) || $calificacion < 0 || $calificacion > 10) {
        $error = 'La calificación debe ser un número entre 0 y 10.';
    } else {
        $sql_check = "SELECT id FROM calificaciones WHERE estudiante_id = :estudiante_id AND materia_id = :materia_id AND trimestre = :trimestre";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':trimestre', $trimestre, PDO::PARAM_STR);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            $sql_update = "UPDATE calificaciones SET calificacion = :calificacion WHERE estudiante_id = :estudiante_id AND materia_id = :materia_id AND trimestre = :trimestre";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':calificacion', $calificacion);
            $stmt_update->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
            $stmt_update->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
            $stmt_update->bindParam(':trimestre', $trimestre, PDO::PARAM_STR);
            $stmt_update->execute();
            $success = 'Calificación actualizada correctamente.';
        } else {
            $sql_insert = "INSERT INTO calificaciones (estudiante_id, materia_id, calificacion, trimestre) VALUES (:estudiante_id, :materia_id, :calificacion, :trimestre)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bindParam(':estudiante_id', $estudiante_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':materia_id', $materia_id, PDO::PARAM_INT);
            $stmt_insert->bindParam(':calificacion', $calificacion);
            $stmt_insert->bindParam(':trimestre', $trimestre, PDO::PARAM_STR);
            $stmt_insert->execute();
            $success = 'Calificación registrada correctamente.';
        }

        if (in_array($trimestre, ['1','2','3'])) {
            calcularNotaFinal($conn, $estudiante_id, $materia_id);
        }
    }
}

$sql_estudiantes = "SELECT e.id, u.nombre FROM estudiantes e JOIN usuarios u ON e.usuario_id = u.id ORDER BY u.nombre";
$stmt_estudiantes = $conn->prepare($sql_estudiantes);
$stmt_estudiantes->execute();
$estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Registrar calificaciones</title>
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
                    <i class="bi bi-pencil-square text-primary me-2"></i>
                    Registrar Calificaciones
                </h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" action="registrar_calificaciones.php" class="card p-3 shadow">
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
                        <label for="materia_id" class="form-label">Materia</label>
                        <select id="materia_id" name="materia_id" class="form-select" required>
                            <option value="" disabled selected>Seleccione una materia</option>
                            <?php foreach ($materias as $mat): ?>
                                <option value="<?php echo $mat['id']; ?>"><?php echo htmlspecialchars($mat['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="trimestre" class="form-label">Trimestre</label>
                        <select id="trimestre" name="trimestre" class="form-select" required>
                            <option value="" disabled selected>Seleccione el trimestre</option>
                            <option value="1">Primer trimestre</option>
                            <option value="2">Segundo trimestre</option>
                            <option value="3">Tercer trimestre</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="calificacion" class="form-label">Calificación (0-10)</label>
                        <input type="number" step="0.01" min="0" max="10" id="calificacion" name="calificacion" class="form-control" required />
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