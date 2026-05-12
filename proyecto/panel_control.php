<?php
session_start();
require_once 'bd.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$stmt = $conn->prepare("SELECT nombre, rol FROM usuarios WHERE id = :id");
$stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit();
}

switch ($usuario['rol']) {
    case 'estudiante':
        header("Location: estudiantes/menu_principal.php");
        exit();
    case 'profesor':
        header("Location: profesores/menu_principal.php");
        exit();
    case 'preceptor':
        header("Location: preceptores/menu_principal.php");
        exit();
    case 'tutor':
        header("Location: tutores/menu_principal.php");
        exit();
    default:
        session_destroy();
        header("Location: login.php");
        exit();
}
