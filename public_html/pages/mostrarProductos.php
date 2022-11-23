<?php
include '../../resources/templates/funciones.php';
include_once '../../resources/templates/header.php';
?>

<?php
$carrito=array();
if(isset($_POST['añadir'])){
    $nombre = $_POST['nombre'];
    $idcliente = $_SESSION['idcliente'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    
    $producto=array('idcliente' => $idcliente, 'nombre' => $nombre, 'precio' => $precio);
    array_push($carrito, $producto);
    var_dump($carrito);
    //$_SESSION['carrito']=array();
    //$_SESSION['carrito']=array_push($_SESSION['carrito'], $producto);
    
}

    var_dump($_SESSION['carrito']);
?>

<?php

mostrar_catalogo_user();

?>

<?php
include_once '../../resources/templates/footer.php';
?>

