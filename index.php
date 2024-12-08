<?php
// Inicia una nueva sesión o reanuda la existente
session_start();

// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Verifica si el formulario ha sido enviado mediante el método POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtiene los datos del formulario (correo y contraseña) enviados por el usuario
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    // Construye una consulta SQL para buscar un usuario con el correo proporcionado
    $sql = "SELECT * FROM Usuarios WHERE correo='$correo'";
    $result = $conn->query($sql); // Ejecuta la consulta

    // Verifica si se encontró algún resultado
    if ($result->num_rows > 0) {
        // Obtiene la información del usuario encontrado
        $usuario = $result->fetch_assoc();

        // Comparación de contraseñas (actualmente sin encriptación, lo que no es seguro)
        if ($contraseña === $usuario['contraseña']) {
            // Almacena el ID del usuario y el rol en variables de sesión
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['id_rol'] = $usuario['id_rol'];

            // Redirige al usuario al panel de control (dashboard)
            header('Location: dashboard.php');
            exit; // Termina la ejecución del script después de la redirección
        } else {
            // Mensaje de error si la contraseña no coincide
            $error = "Contraseña incorrecta";
        }
    } else {
        // Mensaje de error si el usuario no fue encontrado
        $error = "Usuario no encontrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Archivo CSS externo para estilos -->
    <title>Login</title>
</head>
<body>
    <div class="login-container">
        <!-- Formulario de inicio de sesión -->
        <form method="POST" action="">
            <h2>Iniciar Sesión</h2>
            <!-- Muestra un mensaje de error si existe -->
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <input type="email" name="correo" placeholder="Correo" required> <!-- Campo para el correo -->
            <input type="password" name="contraseña" placeholder="Contraseña" required> <!-- Campo para la contraseña -->
            <button type="submit">Ingresar</button> <!-- Botón de envío -->
        </form>
    </div>
</body>
</html>
