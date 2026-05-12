<?php
session_start();
require_once 'bd.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $nueva_contraseña = $_POST['nueva_contraseña'] ?? '';
    $confirmar_contraseña = $_POST['confirmar_contraseña'] ?? '';

    // Verificar que el token no esté vacío
    if (!$token) {
        $error = 'Token inválido.';
    } elseif (!$nueva_contraseña || !$confirmar_contraseña) {
        $error = 'Por favor, complete todos los campos.';
    } elseif ($nueva_contraseña !== $confirmar_contraseña) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        // Verificar el token en la base de datos
        $stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reset) {
            // Verificar si el token ha expirado
            if (strtotime($reset['expires_at']) > time()) {
                // Actualizar la contraseña del usuario
                $userId = $reset['user_id'];
                $hash = password_hash($nueva_contraseña, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("UPDATE usuarios SET contraseña = :contraseña WHERE id = :id");
                $stmt->bindParam(':contraseña', $hash);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();

                // Eliminar el token de la base de datos
                $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = :token");
                $stmt->bindParam(':token', $token);
                $stmt->execute();

                $success = 'Tu contraseña ha sido restablecida exitosamente. Puedes iniciar sesión ahora.';
            } else {
                $error = 'El token ha expirado. Por favor, solicita un nuevo enlace de recuperación.';
            }
        } else {
            $error = 'Token inválido.';
        }
    }
} else {
    $token = $_GET['token'] ?? '';
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Restablecer contraseña</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header text-center">
                        <h4>Restablecer Contraseña</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="restablecer_contraseña.php" novalidate>
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />
                            <div class="mb-3">
                                <label for="nueva_contraseña" class="form-label">Nueva contraseña</label>
                                <input type="password" id="nueva_contraseña" name="nueva_contraseña" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label for="confirmar_contraseña" class="form-label">Confirmar contraseña</label>
                                <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" class="form-control" required />
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Restablecer contraseña</button>
                        </form>
                        <div class="mt-3 text-center">
                            <a href="login.php">Volver al inicio de sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
