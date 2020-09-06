<?php

$data = json_decode(file_get_contents('php://input'), true);

$json =  json_encode($data);

if($data[0] = 1){
            
            require_once 'marca.php';
            require_once 'categorias.php';
            require_once 'producto.php';
            require_once 'option.php';
            $proceso = true;

        if($proceso){
            echo json_encode(array('mensaje' => 'Sincronizacion completa'));

        }else{
            echo json_encode(array('mensaje' => 'No se sincronizaron todos los productos, vuelva a intentarlo'));

        }
         
}


