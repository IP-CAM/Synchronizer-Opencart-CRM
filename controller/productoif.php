<?php
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/productoIF.php';

class ProductControllerIF{

    function getExistProduct($idp){
        $pro = new ProductsIF();
        $proc = $pro->getExistProduct($idp);

        return $proc;
    }

    function deleteProducts(){
        $pro = new ProductsIF();
        $proc = $pro->deleteProducts();
        return $proc;
    }

    function getExistProductsAlmacen(){
        $pro = new ProductsIF();
        $proc = $pro->getExistProductsAlmacen();
        return $proc;
    }

    function getUltimProduc(){
        $pro = new ProductsIF();
        $proc = $pro->getUltimProduc();
        return $proc;
    }

    function insertProduct(Object $prod){

        $pro = new ProductsIF();
        $proc = $pro->insertProduct($prod);

        return $proc;

    }

    function insertProductVariants(Object $prodvariants){

        $pro = new ProductsIF();
        $proc = $pro->insertProductVariants($prodvariants);

        return $proc;

    }

    function getProVar($id){

        $pro = new ProductsIF();
        $proc = $pro->getProVar($id);

        return $proc;

    }

    function getProWareVar($idvar){

        $pro = new ProductsIF();
        $proc = $pro->getProWareVar($idvar);

        return $proc;

    }

    function insertProductWarehousesVariants(Object $prodvariants){

        $pro = new ProductsIF();
        $proc = $pro->insertProductWarehousesVariants($prodvariants);

        return $proc;

    }
	
	function getDescripProduct(){

        $pro = new ProductsIF();
        $proc = $pro->getDescripProduct();

        return $proc;

    }

      
    function UpdateDesc($details,$id){

        $pro = new ProductsIF();
        $proc = $pro->UpdateDesc($details,$id);

        return $proc;

    }

	function getExistProductsCode($code){
        $pro = new ProductsIF();
        $proc = $pro->getExistProductsCode($code);

        return $proc;
    }
	
	function getProVarPrice($name,$product_id){

        $pro = new ProductsIF();
        $proc = $pro->getProVarPrice($name,$product_id);

        return $proc;

    }

	function getCombo($code){
        $pro = new ProductsIF();
        $proc = $pro->getCombo($code);

        return $proc;
    }
	
	function getComboSplit($code){
        $pro = new ProductsIF();
        $proc = $pro->getComboSplit($code);

        return $proc;
    }

    function getItemsCombo($product_id){
        $pro = new ProductsIF();
        $proc = $pro->getItemsCombo($product_id);

        return $proc;
    }

	function getExistProductsCodes($code){
        $pro = new ProductsIF();
        $proc = $pro->getExistProductsCodes($code);

        return $proc;
    }

	function updateProduct(Object $prod){
        $pro = new ProductsIF();
        $proc = $pro->updateProduct($prod);

        return $proc;
    }

	function updateProductVariants(Object $prod){
        $pro = new ProductsIF();
        $proc = $pro->updateProductVariants($prod);

        return $proc;
    }

	function getProVarPriceID($product_id){
        $pro = new ProductsIF();
        $proc = $pro->getProVarPriceID($product_id);

        return $proc;
    }

function getOpcionName($name){
        $pro = new ProductsIF();
        $proc = $pro->getOpcionName($name);

        return $proc;
    }

	


}