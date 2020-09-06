<?php
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/adjustmentsIF.php';

class AdjustmentsControllerIF {

    function Insert(Object $datos){

        $adj = new AdjustmentsIF();
        $adjr = $adj->Insert($datos);
        return $adjr;
    }

    function deleteAdjustmentsIF(){

        $almacen = new AdjustmentsIF();
        
        $alm = $almacen->deleteAdjustmentsIF();

        return $alm;
    }
}