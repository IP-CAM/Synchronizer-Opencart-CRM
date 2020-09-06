<?php

/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/sales.php';

class SalesControllerIF {

    function InsertSales(Object $sma_sales){

        $sales = new Sales();
        $id_sale = $sales->InsertSales($sma_sales);
        return $id_sale;
    }

    function InsertItems(Object $sma_sales_items){

        $sales = new Sales();
        $id_sale = $sales->InsertItems($sma_sales_items);
        return $id_sale;
    }

    function InsertCosting(Object $sma_costing){

        $sales_costing = new Sales();
        $id_salec = $sales_costing->InsertCosting($sma_costing);
        return $id_salec;
    }
    
    public function getPurchase($idpro){ 
       
        $idpu = new Sales();
        $idpur = $idpu->getPurchase($idpro);

        return $idpur;
        
    }

    public function restarProduct(Object $restar){ 
       
        $idpu = new Sales();
        $idpur = $idpu->restarProduct($restar);

        return $idpur;
        
    }

    public function restarProductWarehouse(Object $restar){ 
       
        $idpu = new Sales();
        $idpur = $idpu->restarProductWarehouse($restar);

        return $idpur;
        
    }

    public function restarPurchaseItems(Object $restar){ 
       
        $idpu = new Sales();
        $idpur = $idpu->restarPurchaseItems($restar);

        return $idpur;
        
    }

    public function InsertPago(Object $pago){ 
       
        $pagos = new Sales();
        $idpago = $pagos->InsertPago($pago);
        
        return $idpago;
        
    }

	public function UpdateVentaPago($status,$sale_id,$paid){ 

        $pagos = new Sales();
        $idpago = $pagos->UpdateVentaPago($status,$sale_id,$paid);

        return $idpago;

    }
	
	
    public function restarVariantsProduct(Object $restar){ 
       
        $idpu = new Sales();
        $idpur = $idpu->restarVariantsProduct($restar);

        return $idpur;
        
    }

    public function getWareHouseProductVariants($option){ 
       
        $vware = new Sales();
        $idpro = $vware->getWareHouseProductVariants($option);

        return $idpro;
        
    }

    public function restarWareHouseProductVariants(Object $restar){ 
       
        $idpu = new Sales();
        $idpur = $idpu->restarWareHouseProductVariants($restar);

        return $idpur;
        
    }

	function obtenerReferencia($referencia){
        $resales = new Sales();
        $resalesid = $resales->obtenerReferencia($referencia);

        return $resalesid;
    }

 	function getCaracteresCliente(){

        $cliente = new Sales();
        $idcl = $cliente->getCaracteresCliente();

        return $idcl;

    }

    function UpdateCorrecion($id,$name){

        $cliente = new Sales();
        $idcl = $cliente->UpdateCorrecion($id,$name);

        return $idcl;

    }

	function getSalesItems(){

        $sale = new Sales();
        $idcl = $sale->getSalesItems();

        return $idcl;

    }


    function UpdatePrecio(Object $items){

        $sale = new Sales();
        $idcl = $sale->UpdatePrecio($items);

        return $idcl;

    }

	function UpdatePrecioUnit(Object $items){

        $sale = new Sales();
        $idcl = $sale->UpdatePrecioUnit($items);

        return $idcl;

    }

	
 
}