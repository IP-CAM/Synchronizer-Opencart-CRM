<?php
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/optionqbn.php';
class OptionControllerQbn{

   

    function getOptions($idop){

        $option = new OptionQbn();
        $rows = $option->getOptions($idop);

        return $rows;
    }


    function getOptionValues($idop){

        $option = new OptionQbn();
        $rows = $option->getOptionValues($idop);

        return $rows;
    }

    function getProductOption($idpr){

        $option = new OptionQbn();
        $rows = $option->getProductOption($idpr);

        return $rows;
    }

}