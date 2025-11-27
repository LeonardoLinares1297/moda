<?php

$servername = "localhost";
$username = "root"; // Usuario por defecto de XAMPP
$password = "";     // Contraseña por defecto de XAMPP (vacía)
$dbname = "moda_lilac_db"; // El nombre de la base de datos que creamos

// 1. Verificar si la solicitud es un POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 2. Conexión a la base de datos
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Fallo en la conexión: " . $conn->connect_error);
    }

    // 3. Sanitización y recolección de datos
    // Usa htmlspecialchars para prevenir ataques XSS y real_escape_string para SQL Injection
    $nombre = $conn->real_escape_string(htmlspecialchars($_POST['nombre']));
    $email = $conn->real_escape_string(htmlspecialchars($_POST['email']));
    $tipo_consulta = $conn->real_escape_string(htmlspecialchars($_POST['tipo_consulta']));
    $mensaje = $conn->real_escape_string(htmlspecialchars($_POST['mensaje']));

    // 4. Preparar la consulta SQL (Usando sentencias preparadas para mayor seguridad)
    $sql = "INSERT INTO sugerencias_moda (nombre, email, tipo_consulta, mensaje) VALUES (?, ?, ?, ?)";
    
    // Preparar la sentencia
    $stmt = $conn->prepare($sql);

    // Enlazar los parámetros (s = string)
    if ($stmt === false) {
        die('Error en la preparación de la sentencia: ' . $conn->error);
    }
    $stmt->bind_param("ssss", $nombre, $email, $tipo_consulta, $mensaje);

    // 5. Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir al usuario de vuelta al formulario con un mensaje de éxito (o mostrar éxito en el HTML)
        header("Location: sugerencias.html?status=success");
        exit();
    } else {
        // Mostrar error y redirigir con un mensaje de error
        header("Location: sugerencias.html?status=error");
        error_log("Error al insertar: " . $stmt->error);
        exit();
    }

    // 6. Cerrar la conexión
    $stmt->close();
    $conn->close();
} else {
    // Si alguien accede directamente al script sin enviar datos POST
    echo "Acceso denegado.";
}
?>