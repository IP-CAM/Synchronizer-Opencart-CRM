<?php

require_once 'config/config.php';
require_once 'controller/optionqbn.php';
require_once 'controller/variantsif.php';
require_once 'controller/productoqbn.php';
require_once 'controller/productoif.php';

$opt = new OptionControllerQbn();
$var = new VariantsIFControllerIF();
$pro = new ProductControllerQBN();
$proif = new ProductControllerIF();

$oppro = $proif->getExistProductsAlmacen();


while ($op = $oppro->fetch_object()) {

    $prov = $opt->getProductOption($op->id);
    if( $prov->num_rows > 0 ){
        while ($opvalue = $prov->fetch_object()) {
            
            $result = $var->issetOption($opvalue->product_option_value_id);
            if ($result->num_rows > 0) {
                //echo $opvalue->product_option_value_id." existe<br>";
                $result = $var->issetOption($opvalue->product_option_value_id);

                $objValue = $opt->getOptionValues($opvalue->option_value_id);


                while ($objvariants = $result->fetch_object()){

                    while ($value = $objValue->fetch_object()) {


                        $option = $opt->getOptions($value->option_id);
                        
                        $nombre = $option->fetch_object()->name .' '.$value->name;
                        
                        if($nombre != $objvariants->name){

                           
                            
                            $variantval = new stdClass();
                            $variantval->option_value_id = $opvalue->product_option_value_id;
                            $variantval->name = $nombre ;                    
                            $idv = $var->update($variantval);
                            $idvpro = $proif->updateProductVariants($variantval);

                        }else{
                            //echo 'no hay cambios '.$opvalue->product_option_value_id.'<br>';
                        }
                         
                     }
                }
            } else {
                $objValue = $opt->getOptionValues($opvalue->option_value_id);

                while ($value = $objValue->fetch_object()) {
                    $option = $opt->getOptions($value->option_id);
                   
                    $nombre = $option->fetch_object()->name .' '.$value->name;

                    
                    $variantval = new stdClass();
                    $variantval->option_value_id = $opvalue->product_option_value_id;
                    $variantval->name = $nombre ;                    
                    $idv = $var->Insert($variantval);
                   
                    //var_dump($idv);

                   //INSERTAMOS EL PRODUCT_VARIANTS
                    $product_variant = new stdClass();
                    $product_variant->id = $opvalue->product_option_value_id;
                    $product_variant->product_id = $opvalue->product_id;
                    $product_variant->name = $nombre;
                    $product_variant->cost = valor_cero;
                    $product_variant->price =  $opvalue->price;
                    $product_variant->quantity = valor_cero;
                    $product_variant->warehouse_id  = tienda_dos;
                    $product_variant->rack  = var_null;
                    $idvar = $proif->insertProductVariants($product_variant);
                    //var_dump($idvar);
                    if ($idvar) {
                        $resp = $proif->getProWareVar($idvar);
                        if ($resp->num_rows > 0) {
                            //echo $idvar . " ya existe";
                        } else {
                            $idvarw = $proif->insertProductWarehousesVariants($product_variant);
                        }

                        //var_dump($idvarw);
                    }
                    
                    
                    
                }
                
              
            }
        }
    }else{
        //echo 'no tiene opciones'.$op->id;
    }
    
    
}
