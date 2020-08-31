<?php
require_once "../modelos/Categoria.php";

$categoria = new Categoria();

$idcategoria = isset($_POST["idcategoria"])? limpiarCadena($_POST["idcategoria"]) : "";
$nombre = isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]) : "";
$descripcion = isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (empty($idcategoria)) {  
            $rspta = $categoria->insertar($nombre, $descripcion);
            echo $rspta ? "Categoria registrada" : "Categoria no se pudo registrar";
        } else {
            $rspta = $categoria->editar($idcategoria, $nombre, $descripcion);
            echo $rspta ? "Categoria actualizada" : "Categoria no se pudo actualizar";
        }
    break;
    
    case 'desactivar':
        $rspta = $categoria->desactivar($idcategoria);
        echo $rspta ? "Categoria desactivada" : "Categoria no se pudo desactivar";
    break;
    
    case 'activar':
        $rspta = $categoria->activar($idcategoria);
        echo $rspta ? "Categoria activada" : "Categoria no se pudo activar";
    break;

    case 'mostrar':
        $rspta = $categoria->mostrar($idcategoria);
        // Codificar el resultado utilizando JSON
        echo json_encode($rspta);
    break;

    case 'listar':
        $rspta = $categoria->listar();
        // Vamos a declarar un array
        $data = Array();

        while ($reg = $rspta->fetch_object()) {
            $data[] = array(
                "0" => '<button class="btn btn-warning" onclick="mostrar('.$reg->idcategoria.')"><i class="fa fa-pencil"></i></i></button>',
                "1" => $reg->nombre,
                "2" => $reg->descripcion,
                "3" => $reg->condicion
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