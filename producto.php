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

if (!empty($limit)) {
    $proif = new ProductControllerIF();

    $offset = $proif->getUltimProduc();
    if ($offset->num_rows == 0) {
        $offs = 0;
    } else {
        $offse = $offset->fetch_object();
        $offs = $offse->limite;
    }



    //obtengo los id de productos
    $proid = $procat->getIdProduct();

    //valido si tengo productos
    if ($proid->num_rows > 0) {

        //recorrero los productos
        while ($pross = $proid->fetch_object()) {

            $proif_exist = $proif->getExistProduct($pross->product_id);

            if ($proif_exist->num_rows == 0) {
                $exis_combo = $proif->getComboSplit($pross->model . '-');
                if ($exis_combo->num_rows == 0) {

                    //obtengo su id - categoria - parent del productos enviando cada id de producto
                    //para definir niveles cat-sub-subsub
                    $niveles = $procat->getProducToCate($pross->product_id);

                    while ($nivel = $niveles->fetch_object()) {
                        //almaceno la respuesta en un array para tenerlo en conjunto. Esto es por 1 producto
                        //despues el array se limpia al terminar el proceso de ese producto.
                        array_push($arrc, [
                            "product_id" => $nivel->product_id, "category_id" => $nivel->category_id,
                            "parent_id" => $nivel->parent_id
                        ]);
                    }

                    //si el arrry es mayor a 0 quiere decir que el producto esta asignado a una cat, puede tener 2 o 3 niveles
                    if (count($arrc) > 0) {

                        //recorro el array
                        for ($i = 0; $i < count($arrc); $i++) {

                            //valido que el parent sea diferente de 0 para obtener su segundo
                            if ($arrc[$i]['parent_id'] != 0) {

                                //busco el producto que se importara con su categoria y parent
                                $pro_c = $procat->getProductoCat($arrc[$i]['category_id'], $arrc[$i]['parent_id'], $arrc[$i]['product_id']);

                                //valido que obtenga filas
                                if ($pro_c->num_rows > 0) {

                                    $producto = $pro_c->fetch_object();


                                    //obtengo el parent de la categoria de segundo nivel 
                                    //este parent_id sera la categoria y la categoria pasa a ser la subcategoria
                                    $catPadre = $procat->obtenerParent($producto->parent_id);
                                    $cat = $catPadre->fetch_object();

                                    //creo un objeto 
                                    $productos = new stdClass();
                                    //si el parent_id es 0 entonces 
                                    if ($cat->parent_id == 0) {
                                        //la categoria es el parent del producto y la subcategoria 
                                        //es la categoria del producto
                                        $productos->category_id = $producto->parent_id;
                                        $productos->subcategory_id = $producto->category_id;
                                    } else {
                                        //si no es asi entonces la categoria es el parent del resultado de 
                                        //obtenerParent y la subcategoria es el parent del producto
                                        $productos->category_id = $cat->parent_id;
                                        $productos->subcategory_id = $producto->parent_id;
                                    }

                                    //Helpers es una clase de funciones para hacer determinada funcion
                                    $help = new Helpers();

                                    //obtengo la ruta donde guardo las imagenes concatendo con el nombre de la imagen               
                                    $img = (str_replace('//', '/', from . '/' . $producto->image));


                                    //validamos el campo y lo comparamos con constantes
                                    //esto es para mantener la logica de la tienda con el crm
                                    if ($producto->tax_class_id == tax_class_id) {
                                        $producto->tax_class_id = tax_rate_id;
                                    }

                                    if ($producto->tax_class_id == tax_class_id_dos) {
                                        $producto->tax_class_id = tax_rate_id_dos;
                                    }

                                    if ($producto->tax_class_id == tax_class_id_tres) {
                                        $producto->tax_class_id = tax_rate_id_tres;
                                    }

                                    //paso todos los parametros del producto al objeto creado
                                    $productos->product_id = $producto->product_id;
                                    $productos->parent_id = $producto->parent_id;
                                    $productos->model = $producto->model;
                                    $productos->name = $producto->name;
                                    $productos->unit = unit;
                                    $productos->cost = cost;
                                    $productos->price = $producto->price;
                                    //la funcion MoverImagn funciona con las librerias de imagenes,Upload.php,Image_lib.php
                                    //le paso la ruta completa del directorio donde se guardan 

                                    $productos->image = $help->MoverImagen($img);
                                    $productos->quantity = valor_cero;
                                    $productos->tax_class_id = $producto->tax_class_id;
                                    $productos->details = string_vacio;
                                    $productos->tax_method = valor_uno;
                                    $productos->barcode_symbology = barcode;
                                    $productos->description = html_entity_decode($producto->description);
                                    $productos->brand = $producto->manufacturer_id;
                                    //slug es una funcion para limpiar espacios, tildes, etc
                                    // y determina un alias ese producto ejm : "ropa-bebe"
                                    $productos->slug = $help->slug($producto->name);
                                    $productos->weight_class_id = $producto->weight_class_id;

                                    //inserto el producto                        
                                    $pro = $proif->insertProduct($productos);
                                    if ($pro) {
                                        ////var_dump($pro);
                                    } else {
                                        //var_dump('no insertado '.$producto->product_id);
                                    }
                                } else {
                                    //echo "product mal creado en opencart:<br>";
                                    ////var_dump($arrc[$i]);
                                }
                            } else {

                                //$arrc[$i]['parent_id'] == 0
                                //busco el producto que se importara con su categoria y parent
                                $pro_c = $procat->getProductoCat($arrc[$i]['category_id'], $arrc[$i]['parent_id'], $arrc[$i]['product_id']);

                                //valido que obtenga filas
                                if ($pro_c->num_rows > 0) {

                                    $producto = $pro_c->fetch_object();


                                    //obtengo el parent de la categoria de segundo nivel 
                                    //este parent_id sera la categoria y la categoria pasa a ser la subcategoria
                                    $catPadre = $procat->obtenerParent($producto->parent_id);
                                    $cat = $catPadre->fetch_object();

                                    //creo un objeto 
                                    $productos = new stdClass();
                                    //si el parent_id es 0 entonces 

                                    //la categoria es el parent del producto y la subcategoria 
                                    //es la categoria del producto
                                    $productos->category_id = $producto->category_id;
                                    $productos->subcategory_id = $producto->parent_id;


                                    //Helpers es una clase de funciones para hacer determinada funcion
                                    $help = new Helpers();

                                    //obtengo la ruta donde guardo las imagenes concatendo con el nombre de la imagen               
                                    $img = (str_replace('//', '/', from . '/' . $producto->image));


                                    //validamos el campo y lo comparamos con constantes
                                    //esto es para mantener la logica de la tienda con el crm
                                    if ($producto->tax_class_id == tax_class_id) {
                                        $producto->tax_class_id = tax_rate_id;
                                    }

                                    if ($producto->tax_class_id == tax_class_id_dos) {
                                        $producto->tax_class_id = tax_rate_id_dos;
                                    }

                                    if ($producto->tax_class_id == tax_class_id_tres) {
                                        $producto->tax_class_id = tax_rate_id_tres;
                                    }

                                    //paso todos los parametros del producto al objeto creado
                                    $productos->product_id = $producto->product_id;
                                    $productos->parent_id = $producto->parent_id;
                                    $productos->model = $producto->model;
                                    $productos->name = $producto->name;
                                    $productos->unit = unit;
                                    $productos->cost = cost;
                                    $productos->price = $producto->price;
                                    //la funcion MoverImagn funciona con las librerias de imagenes,Upload.php,Image_lib.php
                                    //le paso la ruta completa del directorio donde se guardan 

                                    $productos->image = $help->MoverImagen($img);
                                    $productos->quantity = valor_cero;
                                    $productos->tax_class_id = $producto->tax_class_id;
                                    $productos->details = string_vacio;
                                    $productos->tax_method = valor_uno;
                                    $productos->barcode_symbology = barcode;
                                    $productos->description = html_entity_decode($producto->description);
                                    $productos->brand = $producto->manufacturer_id;
                                    //slug es una funcion para limpiar espacios, tildes, etc
                                    // y determina un alias ese producto ejm : "ropa-bebe"
                                    $productos->slug = $help->slug($producto->name);
                                    $productos->weight_class_id = $producto->weight_class_id;

                                    //inserto el producto                        
                                    $pro = $proif->insertProduct($productos);
                                    if ($pro) {
                                        ////var_dump('producto insertado '.$pro);
                                    } else {
                                        //var_dump('producto no insertado '.$producto->product_id);
                                    }
                                } else {
                                    //echo "product mal creado en opencart:<br>";
                                    ////var_dump($arrc[$i]);
                                }
                            }
                        }
                    }
                    //si el arry esta vacio quiere decir que son productos que 
                    //no tienen asignados ninguna categoria, estos se migran con categoris y subcategoris 0
                    elseif (empty(count($arrc))) {

                        $producto = $procat->realProducCat($pross->product_id);

                        if ($producto->num_rows > 0) {
                            $prodr = $producto->fetch_object();


                            $productos = new stdClass();

                            $productos->category_id = 0;
                            $productos->subcategory_id = 0;

                            $help = new Helpers();
                            $img = (str_replace('//', '/', from . '/' . $prodr->image));


                            if ($prodr->tax_class_id == tax_class_id) {
                                $prodr->tax_class_id = tax_rate_id;
                            }

                            if ($prodr->tax_class_id == tax_class_id_dos) {
                                $prodr->tax_class_id = tax_rate_id_dos;
                            }

                            if ($prodr->tax_class_id == tax_class_id_tres) {
                                $prodr->tax_class_id = tax_rate_id_tres;
                            }




                            $productos->product_id = $prodr->product_id;
                            $productos->parent_id = 0;
                            $productos->model = $prodr->model;
                            $productos->name = $prodr->name;
                            $productos->unit = unit;
                            $productos->cost = cost;
                            $productos->price = $prodr->price;

                            $productos->image = $help->MoverImagen($img);

                            $productos->quantity = valor_cero;
                            $productos->tax_class_id = $prodr->tax_class_id;
                            $productos->details = string_vacio;
                            $productos->tax_method = valor_uno;
                            $productos->barcode_symbology = barcode;
                            $productos->description = html_entity_decode($prodr->description);
                            $productos->brand = $prodr->manufacturer_id;
                            $productos->slug = $help->slug($prodr->name);
                            $productos->weight_class_id = $prodr->weight_class_id;

                            $pro = $proif->insertProduct($productos);
                            if ($pro) {
                                ////var_dump($pro);
                            } else {
                                //var_dump($prod);
                            }
                        } else {
                            //echo "producto mal creado en opencart: ".$pross->product_id."<br>";
                        }
                    }
                    //limpio el arryy
                    $arrc = [];
                }else{
                    //echo 'el producto ya tiene un combo asignado modelo: '.$pross->model;
                }
            } else {
                //echo 'ya existe '.$pross->product_id."<br>";

            }
        }
    }
}
