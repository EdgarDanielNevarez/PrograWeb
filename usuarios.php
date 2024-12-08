<?php
session_start();
include 'conexion.php';

// Verificar si el usuario tiene permisos de Administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header('Location: dashboard.php');
    exit;
}

// Operaciones CRUD (Crear, Editar, Eliminar)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'];

    if ($accion == 'agregar') {
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $contraseña = password_hash($_POST['contraseña'], PASSWORD_DEFAULT);
        $id_rol = $_POST['id_rol'];

        $sql = "INSERT INTO Usuarios (nombre, correo, contraseña, id_rol) VALUES ('$nombre', '$correo', '$contraseña', $id_rol)";
        $conn->query($sql);
    } elseif ($accion == 'editar') {
        $id_usuario = $_POST['id_usuario'];
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $id_rol = $_POST['id_rol'];

        $sql = "UPDATE Usuarios SET nombre='$nombre', correo='$correo', id_rol=$id_rol WHERE id_usuario=$id_usuario";
        $conn->query($sql);
    } elseif ($accion == 'eliminar') {
        $id_usuario = $_POST['id_usuario'];
        $sql = "DELETE FROM Usuarios WHERE id_usuario=$id_usuario";
        $conn->query($sql);
    }
}

// Obtener lista de usuarios
$sql = "SELECT Usuarios.id_usuario, Usuarios.nombre, Usuarios.correo, Roles.nombre_rol FROM Usuarios
        JOIN Roles ON Usuarios.id_rol = Roles.id_rol";
$usuarios = $conn->query($sql);

// Obtener roles para el formulario
$sql_roles = "SELECT * FROM Roles";
$roles = $conn->query($sql_roles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Gestión de Usuarios</title>
</head>
<body>
    <header>
        <h1>Gestión de Usuarios</h1>
        <a href="dashboard.php">Volver</a>
    </header>
    <main>
        <section>
            <h2>Agregar Usuario</h2>
            <form method="POST">
                <input type="hidden" name="accion" value="agregar">
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="email" name="correo" placeholder="Correo" required>
                <input type="password" name="contraseña" placeholder="Contraseña" required>
                <select name="id_rol" required>
                    <option value="">Seleccionar Rol</option>
                    <?php while ($rol = $roles->fetch_assoc()): ?>
                        <option value="<?php echo $rol['id_rol']; ?>"><?php echo $rol['nombre_rol']; ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit">Agregar</button>
            </form>
        </section>
        <section>
            <h2>Lista de Usuarios</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $usuario['nombre']; ?></td>
                            <td><?php echo $usuario['correo']; ?></td>
                            <td><?php echo $usuario['nombre_rol']; ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="accion" value="editar">
                                    <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                    <button type="button" onclick="editarUsuario(<?php echo htmlspecialchars(json_encode($usuario)); ?>)">Editar</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                    <button type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
    <script>
        function editarUsuario(usuario) {
            const form = document.createElement('form');
            form.method = 'POST';

            form.innerHTML = `
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_usuario" value="${usuario.id_usuario}">
                <input type="text" name="nombre" value="${usuario.nombre}" required>
                <input type="email" name="correo" value="${usuario.correo}" required>
                <select name="id_rol" required>
                    <option value="1" ${usuario.nombre_rol === "Administrador" ? "selected" : ""}>Administrador</option>
                    <option value="2" ${usuario.nombre_rol === "Lector" ? "selected" : ""}>Lector</option>
                </select>
                <button type="submit">Guardar Cambios</button>
                <button type="button" onclick="document.body.removeChild(this.parentElement)">Cancelar</button>
            `;

            document.body.appendChild(form);
        }
    </script>
</body>
</html>
