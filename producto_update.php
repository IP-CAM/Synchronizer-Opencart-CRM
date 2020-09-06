<?php

//require de librerias y controlasdores
require_once 'libraries/Upload.php';
require_once 'libraries/Image_lib.php';
require_once 'controller/productoqbn.php';
require_once 'controller/productoif.php';
require_once 'helpers/helpers.php';
require_once 'system_settings.php';
require_once 'config/config.php';

$procat = new ProductControllerQBN();
$limit = limit;
$arrc = array();

$proif = new ProductControllerIF();




//obtengo los id de productos
$proid = $procat->realProducDesc($fecha);

//valido si tengo productos
if ($proid->num_rows > 0) {

    //recorrero los productos
    while ($pross = $proid->fetch_object()) {
        
        $proif_exist = $proif->getExistProductsCodes($pross->model);

        if (@$proif_exist->num_rows > 0) {


            while($prodif = $proif_exist->fetch_object()){

                $productos = new stdClass();

                if( ($prodif->name != $pross->name) || ($prodif->price != $pross->price) ){

                $productos->name = $pross->name;
                $productos->price = $pross->price;
                $productos->model = $pross->model;  
                $pro = $proif->updateProduct($productos);

                /*$productos->category_id = $cat->parent_id;
                $productos->subcategory_id = $producto->parent_id;
                $help = new Helpers();
                $img = (str_replace('//', '/', from . '/' . $producto->image));

                $producto->tax_class_id = tax_rate_id_tres;
                $productos->product_id = $producto->product_id;
                $productos->parent_id = $producto->parent_id;
                $productos->model = $producto->model;                
                $productos->unit = unit;
                $productos->cost = cost;*/
                /*$productos->image = $help->MoverImagen($img);
                $productos->quantity = valor_cero;
                $productos->tax_class_id = $producto->tax_class_id;
                $productos->details = string_vacio;
                $productos->tax_method = valor_uno;
                $productos->barcode_symbology = barcode;
                $productos->description = html_entity_decode($producto->description);
                $productos->brand = $producto->manufacturer_id;
                $productos->slug = $help->slug($producto->name);
                $productos->weight_class_id = $producto->weight_class_id;*/    
                

                }
               
            }
            
                
           
        } else {
            //echo 'ya existe '.$pross->product_id."<br>";

        }
    }
}
