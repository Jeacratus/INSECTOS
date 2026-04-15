<?php
//agregar.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
//permitir metodos post, get y options (para cors)
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

//codigo para crear una nueva cuenta de usuario
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //manejar la sollicitud options (para cors)
    exit(0);
}
//vincilar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comentariousuario";
//crear conexion a la base de datos
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("conexion fallida: " . $conn->connect_error);
}

//crear base de datos si no existe
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) !== TRUE) {
    die("Error creando base de datos: " . $conn->error);
}

//seleccionar la base de datos
$conn->select_db($dbname);

//crear tabla si no existe
$sql = "CREATE TABLE IF NOT EXISTS comentario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    texto TEXT NOT NULL,
    nombre VARCHAR(255) NOT NULL
)";
if ($conn->query($sql) !== TRUE) {
    die("Error creando tabla: " . $conn->error);
}

//verificar que resibimos los datos del comentario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['texto']) || !isset($_POST['nombre']) || !isset($_POST['insecto'])) {
        echo json_encode(["message" => "Datos incompletos"]);
        exit(0);
    } else {
        $texto = $_POST['texto'];
        $nombre = $_POST['nombre'];
        $insecto = $_POST['insecto'];
        $table = "comentario_" . $insecto;
    }

    //validar que el texto del comentario no este vacio
    if (empty($texto) || empty($nombre) || empty($insecto)) {
        echo json_encode(["message" => "Los campos no pueden estar vacios"]);
        exit(0);
    }

    //crear tabla si no existe
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        texto TEXT NOT NULL,
        nombre VARCHAR(255) NOT NULL
    )";
    if ($conn->query($sql) !== TRUE) {
        die("Error creando tabla: " . $conn->error);
    }

    //preparar la consulta sql para insertar el comentario en la base de datos
    $stmt = $conn->prepare("INSERT INTO $table (texto, nombre) VALUES (?, ?)");
    $stmt->bind_param("ss", $texto, $nombre);
    if ($stmt->execute()) {
        echo json_encode(["message" => "Comentario agregado exitosamente"]);
    } else {
        echo json_encode(["message" => "Error al agregar el comentario: " . $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>

<?php
// Handle GET request to fetch comments
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['insecto'])) {
        echo json_encode(["error" => "Insecto no especificado"]);
        exit;
    }
    $insecto = $_GET['insecto'];
    $table = "comentario_" . $insecto;

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
        exit;
    }

    // Crear tabla si no existe
    $sql_create = "CREATE TABLE IF NOT EXISTS $table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        texto TEXT NOT NULL,
        nombre VARCHAR(255) NOT NULL
    )";
    $conn->query($sql_create);

    $sql = "SELECT nombre, texto FROM $table ORDER BY id DESC";
    $result = $conn->query($sql);

    $comments = [];
    if ($result) {
        while($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    }

    echo json_encode($comments);
    $conn->close();
}
?>
