<?php
// Conexión a la base de datos
include 'conexion.php';

// Inicializar variables para mensaje y tipo de alerta
$mensaje = "";
$tipo_alerta = "";

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar los datos del formulario
    $id_producto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
    $nombre_producto = filter_input(INPUT_POST, 'nombre_producto', FILTER_SANITIZE_SPECIAL_CHARS);
    $precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);

    // Asegurarse de que las cadenas no sean nulas antes de aplicar trim()
    $nombre_producto = $nombre_producto !== null ? trim($nombre_producto) : '';

    // Validaciones más estrictas
    if (!$id_producto || empty($nombre_producto) || !$precio || !$cantidad) {
        $mensaje = "Todos los campos son obligatorios y deben ser válidos.";
        $tipo_alerta = "danger"; // Alerta de error
    } else {
        // Preparar la consulta de actualización de forma segura
        $stmt = $conexion->prepare("UPDATE Productos SET nombre_producto = ?, precio = ?, cantidad = ? WHERE id = ?");

        // Verificar si la consulta se preparó correctamente
        if ($stmt) {
            $stmt->bind_param("sdii", $nombre_producto, $precio, $cantidad, $id_producto); // "sdii" significa: string, double, integer, integer

            // Ejecutar la consulta y verificar el resultado
            if ($stmt->execute()) {
                $mensaje = "Producto modificado exitosamente.";
                $tipo_alerta = "success"; // Alerta de éxito
            } else {
                // No mostrar detalles de error en producción, pero loguear el error en lugar seguro
                error_log("Error al ejecutar la actualización del producto: " . $stmt->error);
                $mensaje = "Ocurrió un error al modificar el producto. Intenta nuevamente.";
                $tipo_alerta = "danger"; // Alerta de error
            }

            $stmt->close(); // Cerrar la declaración
        } else {
            // Manejar error en la preparación de la consulta
            error_log("Error al preparar la consulta SQL: " . $conexion->error);
            $mensaje = "Ocurrió un error al procesar la solicitud. Intenta nuevamente.";
            $tipo_alerta = "danger"; // Alerta de error
        }
    }
}

$conexion->close(); // Cerrar la conexión
?>

<!-- HTML del formulario para modificar productos -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto</title>
    <!-- Incluir Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Modificar Producto</h2>

        <!-- Mostrar mensaje de alerta si existe -->
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= htmlspecialchars($tipo_alerta); ?>"><?= htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <form method="POST" action="modificar_producto.php">
            <div class="form-group">
                <label for="id_producto">ID del Producto</label>
                <input type="number" class="form-control" id="id_producto" name="id_producto" required>
            </div>
            <div class="form-group">
                <label for="nombre_producto">Nombre del Producto</label>
                <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required>
            </div>
            <div class="form-group">
                <label for="precio">Precio</label>
                <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="cantidad">Cantidad</label>
                <input type="number" class="form-control" id="cantidad" name="cantidad" required>
            </div>
            <button type="submit" class="btn btn-warning">Modificar Producto</button>
            <a href="index.php" class="btn btn-secondary">Volver a la Página Principal</a>
        </form>
    </div>

    <!-- Incluir Bootstrap JS y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>