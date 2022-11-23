<?php

session_start();

function conexion_bbdd() {
    //Base de datos de ejemplo
    $cadena_conexion = "mysql:dbname=BBDD_eclipse;host=127.0.0.1";
    $usuario = "root";
    $clave = "";
    try {
        //Se crea la conexión con la base de datos
        $bd = new PDO($cadena_conexion, $usuario, $clave);
        // Opcional en MySQL, dependiendo del controlador 
        // de base de datos puede ser obligatorio
        //$bd->closeCursor(); 
        //echo "Conexion extablecida";
        return $bd;
    } catch (Exception $e) {
        echo "Error al crear la conexion con la BBDD: " . $e->getMessage();
    }
}

function cerrar_sesion_bbdd() {
    $bd = null;
}

function iniciar_sesion($user, $password) {
    try {
        $centinela = false;
        $bd = conexion_bbdd();
        //echo "Conexión realizada con éxito <br>";
        //Se construye la consulta y se guarda en una variable
        $sql = "SELECT nombre, clave, rol FROM clientes";
        //Se ejecuta la consulta y se guarda en una variable
        $usuarios = $bd->query($sql);
        //echo "Número de usuarios: ".$usuarios->rowCount()."<br>";
        //Se recorre el array que nos devuelve la consulta


        foreach ($usuarios as $usu) {

            if ($usu['nombre'] == $user && $usu['clave'] == $password) {
                $centinela = true;
            }
        }
        cerrar_sesion_bbdd();
        return $centinela;
    } catch (Exception $e) {
        echo "Error al iniciar sesion: " . $e->getMessage();
    }
}

function crear_variables_sesion($user, $password) {
    try {

        $bd = conexion_bbdd();
        echo "Conexión realizada con éxito <br>";
        //Se construye la consulta y se guarda en una variable
        $sql = "SELECT nombre, clave, rol , idcliente FROM clientes";
        //Se ejecuta la consulta y se guarda en una variable
        $usuarios = $bd->query($sql);
        echo "Número de usuarios: " . $usuarios->rowCount() . "<br>";
        //Se recorre el array que nos devuelve la consulta
        foreach ($usuarios as $usu) {
            echo 'entro';
            if ($usu['nombre'] == $user && $usu['clave'] == $password) {
                //echo $usuarios['nombre'];
                echo "<h2>Variables de sesion creadas</h2>";
                //Creamos las variables de sesion
                $_SESSION["nombre"] = $usu['nombre'];
                $_SESSION["rol"] = $usu['rol'];
                $_SESSION["idcliente"] = $usu['idcliente'];
            }
        }
        cerrar_sesion_bbdd();
    } catch (Exception $e) {
        echo "Error al iniciar sesion: " . $e->getMessage();
    }
}

function insertar_producto($nombre, $precio, $tipo) {
    try {
        $centinela = false;
        $bd = conexion_bbdd();
        //echo "Conexión realizada con éxito <br>";
        $ins = "insert into productos(nombre, precio, tipo) values ('" . $nombre . "','" . $precio . "','" . $tipo . "');";
        $result = $bd->query($ins);
        if ($result) {
            //echo "insert correcto <br>";
            //echo "Fila(s) insertadas: ".$result->rowCount()."<br>";
            echo "<h2>Producto insertado correctamente</h2>";
        } else {
            print_r($bd->errorinfo());
        }
        //echo "Código de la fila insertada".$bd->lastInsertId()."<br>";
        cerrar_sesion_bbdd();
    } catch (Exception $e) {
        echo "Error al iniciar sesion: " . $e->getMessage();
    }
}

//Funcion que elimina de la BBDD un producto pasandole por parametro el id del producto a eliminar
function borrar_producto($idprod) {
    try {
        $centinela = false;
        $bd = conexion_bbdd();
        //echo "Conexión realizada con éxito <br>";
        $del = "delete from productos where idproducto='" . $idprod . "'";
        $result = $bd->query($del);
        //Se comprueban los errores
        if ($result) {
            echo "<h2>Producto con el ID '" . $idprod . "' eliminado con exito.</h2>";
            //echo "Filas borradas: ".$result->rowCount()."<br>";             
        } else {
            print_r($bd->errorInfo());
        }
        cerrar_sesion_bbdd();
    } catch (Exception $e) {
        echo "Error al iniciar sesion: " . $e->getMessage();
    }
}

//Funcion que devuelve la informacion del producto.
function extraer_informacion_producto($idprod) {
    try {
        $prod = array();
        $bd = conexion_bbdd();
        //echo "Conexión realizada con éxito <br>";
        $con = "select idproducto, nombre, precio, tipo from productos where idproducto='" . $idprod . "'";
        $result = $bd->query($con);
        //Se comprueban los errores
        foreach ($result as $producto) {
            $prod = array('idproducto' => $producto['idproducto'], 'nombre' => $producto['nombre'], 'precio' => $producto['precio'], 'tipo' => $producto['tipo']);
            //var_dump($producto);
        }

        cerrar_sesion_bbdd();
        return $prod;
    } catch (Exception $e) {
        echo "Error al extraer valores: " . $e->getMessage();
    }
}

function modificar_producto($idprod, $nombre, $precio, $tipo) {
    try {
        $bd = conexion_bbdd();
        //echo "Conexión realizada con éxito <br>";
        $upd = "update productos set nombre='" . $nombre . "', precio=" . $precio . ", tipo='" . $tipo . "' where idproducto = " . $idprod . ";";
        $result = $bd->query($upd);
        //comprobamos errores
        if ($result) {
            echo "<h2>El producto con el id '" . $idprod . "' ha sido modificado correctamente</h2><br>";
            //echo "Filas actualizadas: ".$result->rowCount()."<br>";
        } else {
            print_r($bd->errorInfo());
        }
        cerrar_sesion_bbdd();
    } catch (Exception $e) {
        echo "Error al iniciar sesion: " . $e->getMessage();
    }
}

function mostrar_productos_admin() {
    try {
        $bd = conexion_bbdd();
        //echo "Conexión realizada con éxito <br>";

        $sql = "SELECT * from productos";

        $preparada = $bd->prepare($sql);
        $preparada->setFetchMode(PDO::FETCH_ASSOC);
        $preparada->execute();
        //echo "Usuarios con rol 0--> " . $preparada->rowCount() . "<br>";

        echo '<table class="table">';
        //Titulos (thead)
        echo '<thead>
                <tr>
                    <th scope="col">idproducto</th>
                    <th scope="col">Nombre Producto</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Modificar</th>
                    <th scope="col">Borrar</th>
                </tr>
            </thead>';

        //Cuerpo tabla, toda la informacion
        echo '<tbody>';

        while ($usu = $preparada->fetch()) {
            echo '<tr>';
            echo '<th scope="row">' . $usu['idproducto'] . '</th>';
            echo '<td>' . $usu['nombre'] . '</td>';
            echo '<td>' . $usu['precio'] . '</td>';
            echo '<td>' . $usu['tipo'] . '</td>';
            //Enlace modificar
            echo '<td>';
            echo "<form method='post' action= 'modificarProducto.php'>";
            echo "<input type='text' name='idproducto'  value='" . $usu['idproducto'] . "' hidden/>";
            echo "<button class='btn btn-outline-dark' type='submit' name='modificar'>Modificar</button>";
            echo "</form>";
            echo '</td>';
            //enlace borrar
            echo '<td>';
            echo "<form method='post' action= 'modificarBorrarProducto.php'>";
            echo "<input type='text' name='idproducto'  value='" . $usu['idproducto'] . "' hidden/>";
            echo "<button class='btn btn-outline-danger' type='submit' name='borrar'>Eliminar</button>";
            echo "</form>";
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        cerrar_sesion_bbdd();
    } catch (Exception $e) {
        echo "Error al iniciar sesion: " . $e->getMessage();
    }
}

function mostrar_catalogo_user() {
    try {
        $bd = conexion_bbdd();
        //echo "Conexión realizada con éxito <br>";

        $sql = "SELECT * from productos";

        $preparada = $bd->prepare($sql);
        $preparada->setFetchMode(PDO::FETCH_ASSOC);
        $preparada->execute();
        //echo "Usuarios con rol 0--> " . $preparada->rowCount() . "<br>";

        echo '<table class="table">';
        //Titulos (thead)
        echo '<thead>
                <tr>
                    <th scope="col">Productos</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Añadir</th>
                </tr>
            </thead>';

        //Cuerpo tabla, toda la informacion
        echo '<tbody>';

        while ($usu = $preparada->fetch()) {
            echo '<tr>';
            echo '<td>' . $usu['nombre'] . '</td>';
            echo '<td>' . $usu['precio'] . '</td>';
            echo '<td>' . $usu['tipo'] . '</td>';
            echo '<td>' .                   
                    "<form method='post' action= 'mostrarProductos.php'>"
                    . "<input type='number' class='form-control' id='' placeholder='' name='cantidad' value='' min='0' max='100' maxlength='3' required>"
                    
                    . '</td>'; 
            //Enlace modificar
            echo '<td>';
            //echo "<input type='text' name='idcliente'  value='" . $_SESSION['idcliente'] . "' hidden/>";
            echo "<input type='text' name='nombre'  value='" . $usu['nombre'] . "' hidden/>";
            echo "<input type='text' name='precio'  value='" . $usu['precio'] . "' hidden/>";
            echo "<button class='btn btn-outline-dark' type='submit' name='añadir'>Añadir al Carrito</button>";
            echo "</form>";
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo "<form method='post' action= 'modificarProducto.php'>";
        echo "<button class='btn btn-outline-dark' type='submit' name='modificar'>Realizar compra</button>";
        echo "</form>";
        cerrar_sesion_bbdd();
    } catch (Exception $e) {
        echo "Error al iniciar sesion: " . $e->getMessage();
    }
}
?>

