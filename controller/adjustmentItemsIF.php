<?php
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/adjustmentItemsIF.php';

class AdjustmentsItemsControllerIF {

    function Insert(Object $datos){

        $adjitem = new AdjustmentItemsIF();
        $adjri = $adjitem->Insert($datos);
        return $adjri;
    }
    
    function deleteAdjustmentsItems(){

        $adjis = new AdjustmentItemsIF();
        
        $adjit = $adjis->deleteAdjustmentsItems();

        return $adjit;
    }
}