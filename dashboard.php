<?php
// Inicia una nueva sesión o reanuda la existente
session_start();

// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Verifica si el usuario ha iniciado sesión, si no, redirige a la página de inicio de sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php'); // Redirige al formulario de inicio de sesión
    exit; // Finaliza la ejecución del script
}

// Obtiene el ID del usuario y el rol almacenados en la sesión
$id_usuario = $_SESSION['id_usuario'];
$id_rol = $_SESSION['id_rol'];

// Consulta para obtener el nombre del usuario actual
$sql = "SELECT nombre FROM Usuarios WHERE id_usuario = $id_usuario";
$result = $conn->query($sql);
$usuario = $result->fetch_assoc(); // Extrae los datos del usuario

// Determina el rol del usuario basado en el ID de rol
$rol = $id_rol == 1 ? "Administrador" : "Lector"; // 1: Administrador, cualquier otro valor: Lector
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Archivo CSS externo para estilos -->
    <title>Dashboard</title>
</head>
<body>
    <header>
        <!-- Muestra un saludo personalizado con el nombre y rol del usuario -->
        <h1>Bienvenido, <?php echo $usuario['nombre']; ?> (<?php echo $rol; ?>)</h1>
        <!-- Enlace para cerrar sesión -->
        <a href="logout.php" class="logout">Cerrar sesión</a>
    </header>
    <main>
        <?php if ($id_rol == 1): // Si el usuario es Administrador ?>
            <section>
                <h2>Gestión</h2>
                <ul>
                    <!-- Opciones de gestión disponibles para el administrador -->
                    <li><a href="usuarios.php">Gestionar Usuarios</a></li>
                    <li><a href="documentos.php">Gestionar Documentos</a></li>
                </ul>
            </section>
        <?php else: // Si el usuario es Lector ?>
            <section>
                <h2>Documentos Disponibles</h2>
                <!-- Opción para que el lector vea los documentos disponibles -->
                <a href="documentos.php">Ver Documentos</a>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
