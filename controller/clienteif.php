<?php 

/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/clienteif.php';

class ClienteControllerIF{

    function getCliente($email){
        
        $clienteqbn = new ClienteIF();
        $result = $clienteqbn->getCliente($email);

        return $result;

    }

    function Insert(Object $data){

        $cliente = new ClienteIF();
        $id = $cliente->Insert($data);
        return $id;
    }

	function getCaracteresCliente(){

        $cliente = new ClienteIF();
        $idcl = $cliente->getCaracteresCliente();

        return $idcl;

    }

       
    function UpdateCorrecion($id,$address){

        $cliente = new ClienteIF();
        $idcl = $cliente->UpdateCorrecion($id,$address);

        return $idcl;

    }
}