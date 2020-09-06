<?php

$data = json_decode(file_get_contents('php://input'), true);

$json =  json_encode($data);

if($data[0] = 1){
	        $fecha = $_GET['fecha'];
            require_once 'producto_update.php';
            $proceso = true;

        if($proceso){
            echo json_encode(array('mensaje' => 'Actualizacion completa'));

        }else{
            echo json_encode(array('mensaje' => 'No se actualizaron todos los productos, vuelva a intentarlo'));

        }
         
}


