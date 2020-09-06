<?php
require_once 'config/config.php';
require_once 'controller/productoif.php';
require_once 'controller/sales.php';
require_once 'controller/variantsif.php';
require_once 'log.php';
require_once 'helpers/mail.php';


$sale = new SalesControllerIF();

$pro = new ProductControllerIF();
$items = $sale->getSalesItems();

while ($item = $items->fetch_object()) {


    $prod = $pro->getExistProductsCode($item->product_code);

    while ($prod_pre = $prod->fetch_object()) {

        $prodvid = $pro->getProVarPriceID($prod_pre->id);

            if(@$prodvid->num_rows == 0){


            $items_sas = new stdClass();
            $items_sas->net_unit_price = round($item->real_unit_price , 2, PHP_ROUND_HALF_UP);
            $items_sas->id =  $item->id;
            
            $items_sas->real_unit_price = round($prod_pre->price , 2, PHP_ROUND_HALF_UP);
            $price_unit = $item->real_unit_price;

            if ($item->tax_rate_id == 2) {
                $items_sas->unit_price =  round($price_unit + ($price_unit * 0.10), 2, PHP_ROUND_HALF_UP);
                $items_sas->item_tax =  round(($price_unit * 0.10),2, PHP_ROUND_HALF_UP)* $item->unit_quantity;

            }

            if ($item->tax_rate_id == 3) {
                $items_sas->unit_price =  round($price_unit + ($price_unit * 0.04), 2, PHP_ROUND_HALF_UP);
                $items_sas->item_tax =    round(($price_unit * 0.04),2, PHP_ROUND_HALF_UP)* $item->unit_quantity;
            }

            if ($item->tax_rate_id == 4) {
                $items_sas->unit_price =  round($price_unit + ($price_unit * 0.21), 2, PHP_ROUND_HALF_UP);
                $items_sas->item_tax =   round(($price_unit * 0.21),2, PHP_ROUND_HALF_UP)* $item->unit_quantity;
            }
            
            $items_sas->sub_total = round($items_sas->unit_price * $item->unit_quantity, 2, PHP_ROUND_HALF_UP);

            $items_sas->option_id = 'NULL';
           
            $re = $sale->UpdatePrecioUnit($items_sas);
            var_dump($re);
            
        }
        
           /* $items_sa = new stdClass();
            $items_sa->real_unit_price =  $prod_pre->price;
            $items_sa->id =  $item->id;
            $re = $sale->UpdatePrecio($items_sa);*/
    
    }
}
