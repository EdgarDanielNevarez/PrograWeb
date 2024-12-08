<?php
// Iniciar sesión para usar las variables de sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php'); // Redirigir al login si no está logueado
    exit;
}

// Obtener el rol del usuario para determinar los permisos
$id_rol = $_SESSION['id_rol'];

// Solo los Administradores (id_rol == 1) pueden realizar gestión de documentos
if ($id_rol == 1 && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion']; // Determinar la acción solicitada: agregar o eliminar

    if ($accion == 'agregar') {
        // Obtener los datos enviados por el formulario
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $archivo = $_FILES['archivo'];

        // Verificar si la carpeta 'uploads/' existe, si no, se crea
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true); // Crear carpeta con permisos de escritura
        }

        // Construir la ruta completa donde se guardará el archivo
        $ruta_archivo = "uploads/" . basename($archivo['name']);

        // Mover el archivo desde la ubicación temporal a la carpeta de destino
        if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
            // Insertar los datos del documento en la base de datos
            $sql = "INSERT INTO Documentos (titulo, descripcion, archivo) VALUES ('$titulo', '$descripcion', '$ruta_archivo')";
            $conn->query($sql);
        } else {
            echo "Error al subir el archivo.";
        }
    } elseif ($accion == 'eliminar') {
        // Obtener el ID del documento a eliminar
        $id_documento = $_POST['id_documento'];

        // Eliminar el documento de la base de datos
        $sql = "DELETE FROM Documentos WHERE id_documento = $id_documento";
        $conn->query($sql);
    }
}

// Consultar todos los documentos para mostrarlos en la lista
$sql = "SELECT * FROM Documentos";
$documentos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Estilos CSS -->
    <title>Documentos</title>
</head>
<body>
    <header>
        <h1>Documentos</h1>
        <a href="dashboard.php">Volver</a> <!-- Enlace para regresar al dashboard -->
    </header>
    <main>
        <!-- Sección de agregar documentos, solo visible para administradores -->
        <?php if ($id_rol == 1): ?>
            <section>
                <h2>Agregar Documento</h2>
                <form method="POST" enctype="multipart/form-data">
                    <!-- Campo oculto para especificar la acción -->
                    <input type="hidden" name="accion" value="agregar">
                    <input type="text" name="titulo" placeholder="Título" required> <!-- Título del documento -->
                    <textarea name="descripcion" placeholder="Descripción"></textarea> <!-- Descripción del documento -->
                    <input type="file" name="archivo" accept="application/pdf" required> <!-- Archivo PDF -->
                    <button type="submit">Subir</button> <!-- Botón para enviar el formulario -->
                </form>
            </section>
        <?php endif; ?>

        <!-- Sección de lista de documentos -->
        <section>
            <h2>Lista de Documentos</h2>
            <ul>
                <!-- Mostrar cada documento disponible -->
                <?php while ($doc = $documentos->fetch_assoc()): ?>
                    <li>
                        <strong><?php echo $doc['titulo']; ?></strong> 
                        - <a href="<?php echo $doc['archivo']; ?>" target="_blank">Ver</a> <!-- Enlace para ver el archivo -->
                        
                        <!-- Botón de eliminar, solo visible para administradores -->
                        <?php if ($id_rol == 1): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="accion" value="eliminar"> <!-- Acción eliminar -->
                                <input type="hidden" name="id_documento" value="<?php echo $doc['id_documento']; ?>"> <!-- ID del documento -->
                                <button type="submit">Eliminar</button> <!-- Botón para eliminar -->
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>
</body>
</html>
