<?php

//require de todos los  controladores a usar
require_once 'controller/productoif.php';
require_once 'controller/categoriaif.php';
require_once 'controller/marcaIF.php';
require_once 'controller/almacenIF.php';
require_once 'controller/adjustmentsIF.php';
require_once 'controller/adjustmentItemsIF.php';

//instancia de objetos
$catif = new CategoriaControllerIF();
$proif = new ProductControllerIF();
$marc = new MarcaControllerIF();
$alm = new AlmacenControllerIF();
$adj = new AdjustmentsControllerIF();
$adji = new AdjustmentsItemsControllerIF();


//pasmospor GET el nombre de la tabla a limpiar.
// si no se se le pasa validara y termina el proceso.
if(empty($_GET['tabla']) || !isset($_GET['tabla'])){
    //echo 'ingrese una tabla para limpiar';
    exit();

}

//tabla de categorias
if($_GET['tabla'] == 'sma_categories'){
    $cat = $catif->delete();
    if($cat){
        //echo "se limipio sma_categories<br>";
    }
}

//tabla de productos
if($_GET['tabla'] == 'sma_products'){
    $p = $proif->deleteProducts();
    if($p){
        //echo "se limipio sma_products<br>";
    }
}

//tabla de marcas
if($_GET['tabla'] == 'sma_brands'){
    $brand = $marc->deleteMarcaIF();
    if($brand){
        //echo "se limipio sma_brands<br>";
    }
}

//tabla de almacen producto
if($_GET['tabla'] == 'sma_warehouses_products'){
    $almc = $alm->deleteAlmacenIF();
    if($almc){
        //echo "se limipio sma_warehouses_products<br>";
    }
}

//tabla de ajuste cantidades
if($_GET['tabla'] == 'sma_adjustments'){
    $adjs = $adj->deleteAdjustmentsIF();
    if($adjs){
        //echo "se limipio sma_adjustments<br>";
    }
}
//tabla de ajuste cantidades producto

if($_GET['tabla'] == 'sma_adjustment_items'){
    $adji = $adji->deleteAdjustmentsItems();
    if($adji){
        //echo "se limipio sma_adjustment_items<br>";
    }
}


