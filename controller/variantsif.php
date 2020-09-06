<?php
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/variantsif.php';
class VariantsIFControllerIF{

    function issetOption($idoption){

        $option = new VariantsIF();
        $result = $option->issetOption($idoption);
        
        return $result;
    }

	function issetOptionName($name){

        $option = new VariantsIF();
        $result = $option->issetOptionName($name);
        
        return $result;
    }
    

    function Insert(Object $variantval){

        $option = new VariantsIF();
        $result = $option->Insert($variantval);
        
        return $result;
    }
	
	function update(Object $variantval){

        $option = new VariantsIF();
        $result = $option->update($variantval);
        
        return $result;
    }

}