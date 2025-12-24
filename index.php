<?php
include_once 'controller/homeController.php';
include_once 'controller/usuarioController.php';
include_once 'controller/apiController.php';

$defaultController = 'home';
$defaultAction = 'ver_home';

session_start();

if (isset($_GET['controller'])) {
    $nombre_controller = $_GET['controller'].'Controller';
    if (class_exists($nombre_controller)) {
        $controller = new $nombre_controller();
        $action = $_GET['action'];
        if (isset($action) && method_exists($controller, $action)) {
            $controller->$action();
        } else {
            header("Location:404.php");
        }
    } else {
        header("Location:404.php");
    }
} else {
    $controller = new homeController();
    $controller->ver_home();
}