<?php
require_once 'libraries/Upload.php';
require_once 'libraries/Image_lib.php';
require_once 'system_settings.php';
require_once 'helpers/helpers.php';
require_once 'config/config.php';
require_once 'controller/marcaQBN.php';
require_once 'controller/marcaIF.php';


$marc = new MarcaControllerQBN();

$marcas = $marc->getMarcaQBN();

while ($mrc = $marcas->fetch_object()) {

    $help = new Helpers();
    
    $img = (str_replace('//', '/', from . '/' . $mrc->image));

    $marca = new stdClass();
    $marca->id = $mrc->manufacturer_id;
    $marca->code = $mrc->manufacturer_id . '-' . $help->slug($mrc->name);
    $marca->name  = $mrc->name;
    $marca->image = $help->MoverImagen($img);
    $marca->slug = $help->slug($mrc->name);
    $marca->description = string_vacio;

    $marcs = new MarcaControllerIF();
    $m = $marcs->insertMarc($marca);
    //var_dump($marca->id.': '.$m);
             
    

}
