<?php
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/purchaseItems.php';

class purchaseItemsController{

    function Insert(Object $data){
        $pur = new purchaseItems();
        $puri = $pur->Insert($data);

        return $puri;
    }

}