<?php
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/almacenIF.php';

class AlmacenControllerIF{
   

    function insertAlmacen_uno(Object $almacens){
        
        $almacen = new AlmacenIF();
        $alm = $almacen->insertAlmacen_uno($almacens);

        return $alm;
    }

    function insertAlmacen_dos(Object $almacens){
        
        $almacen = new AlmacenIF();
        $alm = $almacen->insertAlmacen_dos($almacens);

        return $alm;
    }
	
	function issetProduct($idp){

        $almacen = new AlmacenIF();
        
        $alm = $almacen->issetProduct($idp);

        return $alm;
    }

    function deleteAlmacenIF(){

        $almacen = new AlmacenIF();
        
        $alm = $almacen->deleteAlmacenIF();

        return $alm;
    }


}
