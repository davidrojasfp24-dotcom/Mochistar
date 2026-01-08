<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="main.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['usuario'])) {
    
    $usuario = $_SESSION['usuario'];
    
    if ($usuario->getRol() === 'admin') { 
        
        include 'view/nav/nav_admin.php';
        
    } else {
        
        include 'view/nav/nav_logged.php';
    }
    
} else {
    
    include 'view/nav/nav_casual.php';
}

?>
<div class="separar"></div>

<?php 
include_once $view; 
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>