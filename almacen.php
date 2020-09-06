<?php

require_once 'controller/almacenIF.php';
require_once 'controller/productoif.php';
require_once 'config/config.php';
require_once 'controller/adjustmentsIF.php';
require_once 'controller/adjustmentItemsIF.php';
require_once 'controller/purchaseItems.php';

$almacens = new AlmacenControllerIF();
$proid = new ProductControllerIF();
$adjust = new AdjustmentsControllerIF();
$adjustit = new AdjustmentsItemsControllerIF();
$pur = new purchaseItemsController();

$pro_al = $proid->getExistProductsAlmacen();

if ($pro_al->num_rows > 0) {
    while ($prods = $pro_al->fetch_object()) {

        $alids = $almacens->issetProduct($prods->id);
        if ($alids->num_rows > 0) {
			
            //echo "ya existe" . $alids->fetch_object()->product_id;
        } else {
            $almacen = new stdClass();
            $almacen->product_id  = $prods->id;
            $almacen->reference_no  = $prods->code;
            $almacen->warehouse_id  = tienda_uno;
            $almacen->quantity = valor_cero;
            $almacen->rack =  var_null;
            $almacen->avg_cost = $prods->cost;
            $almacen->note = string_vacio;
            $almacen->attachment = var_null;
            $almacen->created_by = valor_dos;
            $almacen->updated_by = var_null;
            $almacen->updated_at = var_null;
            $almacen->count_id = var_null;

            //$alm = $almacens->insertAlmacen_uno($almacen);
            $almacen->warehouse_id  = tienda_dos;
            $almacen->quantity  = valor_cero;

            $alm = $almacens->insertAlmacen_dos($almacen);
            $idadj = $adjust->Insert($almacen);
            //var_dump($idadj);
            if (!empty($idadj)) {
                $adjitems = new stdClass();
                $adjitems->adjustment_id  = $idadj;
                $adjitems->product_id  = $prods->id;
                $adjitems->option_id  = var_null;
                $adjitems->quantity =  valor_cero;
                $adjitems->warehouse_id  = tienda_dos;
                $adjitems->serial_no =  string_vacio;
                $adjitems->type = addition;
                $idaji = $adjustit->Insert($adjitems);
                //var_dump($idaji);
                //sma_purchase_items
                $pur_item = new stdClass();
                $pur_item->product_id = $prods->id;
                $pur_item->product_code = $prods->code;
                $pur_item->name = $prods->name;
                $pur_item->quantity = valor_cero;
                $pur_item->net_unit_cost = valor_cero;
                $pur_item->warehouse_id = tienda_dos;
                $pur_item->item_tax = valor_cero;
                if ($prods->tax_rate == 2) {
                    $pur_item->tax_rate = 2;
                    $pur_item->tax = '10%';     
                }

                if ($prods->tax_rate == 3) {
                    $pur_item->tax_rate = 3;
                    $pur_item->tax = '4%'; 
                }

                if ($prods->tax_rate == 4) {
                    $pur_item->tax_rate = 4;
                    $pur_item->tax = '21%';         
                }
            
                $pur_item->product_unit_id = valor_uno;
                $purc_item = $pur->Insert($pur_item);
                //var_dump($purc_item);
            }
        }
    }
}
