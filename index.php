<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Alumnos - Bootstrap + PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Lista de Alumnos</h1>

    <?php
    // Datos de conexión a la base de datos
    $host = "localhost";
    $usuario = "root";
    $contrasena = "";
    $base_datos = "escuela_db"; // Cambiado aquí

    // Crear la conexión a la base de datos
    $conn = new mysqli($host, $usuario, $contrasena, $base_datos);

    if ($conn->connect_error) {
        die('<div class="alert alert-danger">Conexión fallida: ' . $conn->connect_error . '</div>');
    }

    // Insertar un nuevo alumno si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insertar'])) {
        $apellido = trim($_POST['apellido']);
        $nombre = trim($_POST['nombre']);
        $aula = trim($_POST['aula']);
        $usuario = trim($_POST['usuario']);

        if ($apellido === '' || $nombre === '' || $aula === '' || $usuario === '') {
            echo '<div class="alert alert-danger">Por favor, completa todos los campos.</div>';
        } else {
            // Insertar datos usando una consulta preparada
            $sql_insert = "INSERT INTO persona (apellido, nombre, aula, usuario) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql_insert);

            if ($stmt) {
                $stmt->bind_param("ssss", $apellido, $nombre, $aula, $usuario);
                if ($stmt->execute()) {
                    echo '<div class="alert alert-success">Alumno insertado correctamente.</div>';
                } else {
                    echo '<div class="alert alert-danger">Error al insertar datos: ' . $stmt->error . '</div>';
                }
                $stmt->close();
            } else {
                echo '<div class="alert alert-danger">Error en la preparación de la consulta.</div>';
            }
        }
    }

    // Consultar los alumnos registrados
    $sql = "SELECT pk_alumno, apellido, nombre, aula, usuario FROM persona";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        echo '<table class="table table-bordered">';
        echo '<thead class="table-dark">';
        echo '<tr>';
        echo '<th>ID Alumno</th>';
        echo '<th>Apellido</th>';
        echo '<th>Nombre</th>';
        echo '<th>Aula</th>';
        echo '<th>Usuario</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        // Filas alternadas con colores (bg-primary, bg-success, etc)
        $clases = ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-info'];
        $i = 0;

        while ($fila = $resultado->fetch_assoc()) {
            $clase_fila = $clases[$i % count($clases)];
            echo "<tr class='{$clase_fila}'>";
            echo '<td>' . $fila['pk_alumno'] . '</td>';
            echo '<td>' . htmlspecialchars($fila['apellido']) . '</td>';
            echo '<td>' . htmlspecialchars($fila['nombre']) . '</td>';
            echo '<td>' . htmlspecialchars($fila['aula']) . '</td>';
            echo '<td>' . htmlspecialchars($fila['usuario']) . '</td>';
            echo '</tr>';
            $i++;
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<div class="alert alert-info">No hay alumnos registrados.</div>';
    }

    // Cerrar la conexión
    $conn->close();
    ?>

    <div class="mt-5">
        <h2>Ingresar Nuevo Alumno</h2>
        <form action="index.php" method="POST" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" required>
                <div class="invalid-feedback">Por favor, ingresa el apellido.</div>
            </div>

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
                <div class="invalid-feedback">Por favor, ingresa el nombre.</div>
            </div>

            <div class="mb-3">
                <label for="aula" class="form-label">Aula</label>
                <input type="text" class="form-control" id="aula" name="aula" required>
                <div class="invalid-feedback">Por favor, ingresa el aula.</div>
            </div>

            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
                <div class="invalid-feedback">Por favor, ingresa el usuario.</div>
            </div>

            <button type="submit" name="insertar" class="btn btn-primary">Insertar Datos</button>
        </form>
    </div>
</div>

<script>
// Validación de Bootstrap 5 para formulario
(() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

</body>
</html>
