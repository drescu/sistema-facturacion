<?php
require_once "../modelos/Articulo.php";

$carticulo = new Articulo();

$idarticulo = isset($_POST["idarticulo"])? limpiarCadena($_POST["idarticulo"]) : "";
$idcategoria = isset($_POST["idcate$idcategoria"])? limpiarCadena($_POST["idcate$idcategoria"]) : "";
$codigo = isset($_POST["codigo"])? limpiarCadena($_POST["codigo"]) : "";
$nombre = isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]) : "";
$stock = isset($_POST["stock"])? limpiarCadena($_POST["stock"]) : "";
$descripcion = isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]) : "";
$imagen = isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':

        if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
            $imagen = "";
        } else {
            // obtengo la extension de la imagen
            $ext = explode('.', $_FILES['imagen']['tmp_name']);
            if($_FILES['imagen']['tmp_name'] == "image/jpg" || $_FILES['imagen']['tmp_name'] == "image/jpeg" ||
            $_FILES['imagen']['tmp_name'] == "image/png")
            {
                $imagen = round(microtime(true)).'.'.end($ext);
                move_uploaded_file($_FILES['imagen']['tmp_name'], "../files/articulos/".$imagen);
            }
        }
        

        if (empty($idarticulo)) {  
            $rspta = $carticulo->insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
            echo $rspta ? "Artículo registrado" : "Artículo no se pudo registrar";
        } else {
            $rspta = $carticulo->editar($idarticulo, $idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
            echo $rspta ? "Artículo actualizado" : "Artículo no se pudo actualizar";
        }
    break;
    
    case 'desactivar':
        $rspta = $carticulo->desactivar($idarticulo);
        echo $rspta ? "Artículo desactivado" : "Artículo no se pudo desactivar";
    break;
    
    case 'activar':
        $rspta = $carticulo->activar($idarticulo);
        echo $rspta ? "Artículo activado" : "Artículo no se pudo activar";
    break;

    case 'mostrar':
        $rspta = $carticulo->mostrar($idarticulo);
        // Codificar el resultado utilizando JSON
        echo json_encode($rspta);
    break;

    case 'listar':
        $rspta = $articulo->listar();
        // Vamos a declarar un array
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => ($reg->condicion) ?  
                            // condicion verdadera
                            '<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')">
                            <i class="fa fa-pencil"></i></i></button>'.
                            ' <button class="btn btn-danger" onclick="desactivar('.$reg->idarticulo.')">
                            <i class="fa fa-close"></i></i></button>' 
                            //  condicion falsa
                            : '<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')">
                            <i class="fa fa-pencil"></i></i></button>'.
                            ' <button class="btn btn-primary" onclick="activar('.$reg->idarticulo.')">
                            <i class="fa fa-check"></i></i></button>',
                "1" => $reg->nombre,
                "2" => $reg->categoria,
                "3" => $reg->codigo,
                "4" => $reg->stock,
                "5" => "<img src='../files/articulos/'".$reg->imagen."height='50px' width='50px'>",
                "6" => ($reg->condicion) ? '<span class="label bg-green">Activado</span>'
                                         : '<span class="label bg-red">Desactivado</span>'
            );
        }
        $results = array(
            "sEcho" => 1, // Informacion para el datatables
            "iTotalRecords" => count($data), // enviamos el total de registros al datatables
            "iTotalDisplayRecords" => count($data), // enviamos el total de registros a visualizar
            "aaData" => $data     // envio el array completo
        );
        echo json_encode($results);
    break;
}

?>