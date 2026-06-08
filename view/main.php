<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : "Mochistar - Mochi Artesanal"; ?></title>
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="view/main.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-black">

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
<div id="page-content">

<?php 
include_once $view; 
?>
</div>
<footer>
    <?php
    include 'view/footer/footer.php';
    ?>
</footer>
<script src="view/carrito-utils.js"></script>
<script src="view/nav/nav.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>