<?php 
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/marcaIF.php';

class MarcaControllerIF{ 

    public function deleteMarcaIF(){ 
       
        $marca = new MarcaIF();
        $brand = $marca->deleteMarcaIF();
        return $brand;
    }

    public function insertMarc(Object $marcas){ 
        
        $marca = new MarcaIF();
        $brand = $marca->insertMarc($marcas);
        return $brand;
    }

}

 