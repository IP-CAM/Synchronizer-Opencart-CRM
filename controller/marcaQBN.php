<?php 
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/marcaQBN.php';

class MarcaControllerQBN{ 

    public function getMarcaQBN(){ 
       
        $marca = new MarcaQBN();
        $result = $marca->getMarcaQBN();
        return $result;
    }

   

}