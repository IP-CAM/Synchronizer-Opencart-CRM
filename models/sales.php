<?php

class Sales
{

    function __construct()
    {
        $this->db = DataBaseIF::connect();
    }

    function InsertSales(Object $sma_sales)
    {

        $sql = "INSERT INTO sma_sales (id, date, reference_no, customer_id, 
        customer, biller_id, biller, warehouse_id, note, staff_note, total, 
        product_discount, order_discount_id, total_discount, order_discount,
         product_tax, order_tax_id, order_tax, total_tax, shipping, grand_total,
          sale_status, payment_status, payment_term, due_date, created_by,
           updated_by, updated_at, total_items, pos, paid, return_id, surcharge,
            attachment, return_sale_ref, sale_id, return_sale_total, rounding, 
            suspend_note, api, shop, address_id, reserve_id, hash, manual_payment,
             cgst, sgst, igst, payment_method) 
             VALUES (NULL, current_timestamp(), 
             '$sma_sales->reference_no',
             $sma_sales->customer_id,
             '$sma_sales->customer',
             $sma_sales->biller_id,
             '$sma_sales->biller',
             $sma_sales->warehouse_id,
             '$sma_sales->note',
             '$sma_sales->staff_note',
             $sma_sales->total,$sma_sales->product_discount,'$sma_sales->order_discount_id',$sma_sales->total_discount,
             $sma_sales->order_discount,$sma_sales->product_tax,$sma_sales->order_tax_id,$sma_sales->order_tax,$sma_sales->total_tax,
             $sma_sales->shipping,$sma_sales->grand_total,'$sma_sales->sale_status','$sma_sales->payment_status',
             $sma_sales->payment_term,$sma_sales->due_date,$sma_sales->created_by,$sma_sales->updated_by,$sma_sales->updated_at,
             $sma_sales->total_items,$sma_sales->pos,$sma_sales->paid,$sma_sales->return_id,$sma_sales->surcharge,$sma_sales->attachment,
             $sma_sales->return_sale_ref,$sma_sales->sale_id,$sma_sales->return_sale_total,$sma_sales->rounding,$sma_sales->suspend_note,
             $sma_sales->api,$sma_sales->shop,$sma_sales->address_id,$sma_sales->reserve_id,'$sma_sales->hash',$sma_sales->manual_payment,
             $sma_sales->cgst,$sma_sales->sgst,$sma_sales->igst,'$sma_sales->payment_method')";


        $result = $this->db->query($sql);
        if ($result) {
            return $this->db->insert_id;
        }

        return $result;
    }


    function InsertItems(Object $sma_sales_items)
    {

        $sql = "INSERT INTO sma_sale_items (id, sale_id, product_id, product_code, 
        product_name, product_type, option_id, net_unit_price, unit_price, quantity, 
        warehouse_id, item_tax, tax_rate_id, tax, discount, item_discount, subtotal, 
        serial_no, real_unit_price, sale_item_id, product_unit_id, product_unit_code, 
        unit_quantity, comment, gst, cgst, sgst, igst) 
        VALUES (NULL, 
        $sma_sales_items->sale_id,
        $sma_sales_items->product_id,
        '$sma_sales_items->product_code',
        '$sma_sales_items->product_name',
        '$sma_sales_items->product_type',
        $sma_sales_items->option_id,
        $sma_sales_items->net_unit_price,
        $sma_sales_items->unit_price,
        $sma_sales_items->quantity,
        $sma_sales_items->warehouse,
        $sma_sales_items->item_tax,
        $sma_sales_items->tax_rate_id,
        '$sma_sales_items->tax',
        '$sma_sales_items->discount',
        $sma_sales_items->item_discount,
        $sma_sales_items->sub_total,      
        '$sma_sales_items->serial_no',
        $sma_sales_items->real_unit_price,
        $sma_sales_items->sale_item_id,
        $sma_sales_items->product_unit_id,
        '$sma_sales_items->product_unit_code',
        $sma_sales_items->unity_quantity,
        '$sma_sales_items->comment',
        '$sma_sales_items->gst',
        $sma_sales_items->cgst,
        $sma_sales_items->sgst,
        $sma_sales_items->igst)";
        
        $result = $this->db->query($sql);
        if ($result) {
            return $this->db->insert_id;
        }

        return $result;
    }



    function InsertCosting(Object $sma_costing)
    {

        $sql = "INSERT INTO sma_costing (id, date, product_id, 
        sale_item_id, sale_id, purchase_item_id, quantity, 
        purchase_net_unit_cost, purchase_unit_cost, sale_net_unit_price, 
        sale_unit_price, quantity_balance, inventory,
         overselling, option_id) 
         VALUES (NULL, 
        current_timestamp(),        
        $sma_costing->product_id,
        $sma_costing->sale_item_id,
        $sma_costing->sale_id,
        $sma_costing->purchase_item_id,
        $sma_costing->quantity,
        $sma_costing->purchase_net_unit_cost,
        $sma_costing->purchase_unit_cost,
        $sma_costing->sale_net_unit_price,
        $sma_costing->sale_unit_price,
        $sma_costing->quantity_balance,
        $sma_costing->inventory,
        $sma_costing->overselling,
        $sma_costing->option_id)";

        $result = $this->db->query($sql);
        if ($result) {
            return $this->db->insert_id;
        }

        return $result;
    }

    function getPurchase($idpro)
    {

        $sql = "SELECT * FROM sma_purchase_items WHERE product_id = $idpro";

        $categoria = $this->db->query($sql);
        return $categoria;
    }

    function restarProduct(Object $resta)
    {

        $sql = "UPDATE sma_products SET quantity = (SELECT (sma_prods.quantity - $resta->quantity) FROM
        (SELECT * FROM sma_products) AS  sma_prods WHERE sma_prods.id = $resta->product_id) WHERE id = $resta->product_id";
		
    
        $result = $this->db->query($sql);

        return $result;
    }

    function restarProductWarehouse(Object $resta)
    {

        $sql = "UPDATE sma_warehouses_products SET quantity = (SELECT (sma_war_prods.quantity - $resta->quantity) 
        FROM (SELECT * FROM sma_warehouses_products) AS  sma_war_prods WHERE sma_war_prods.product_id = $resta->product_id AND sma_war_prods.warehouse_id = 2) WHERE product_id = $resta->product_id AND warehouse_id = 2";

        
        $result = $this->db->query($sql);
        return $result;
    }

    function restarPurchaseItems(Object $resta)
    {

        $sql = "UPDATE sma_purchase_items SET quantity_balance = (SELECT (quantity_balance - $resta->quantity) 
        FROM sma_purchase_items WHERE product_id = $resta->product_id  OR option_id =  $resta->option_id) WHERE product_id = $resta->product_id OR option_id =  $resta->option_id";

    
        $result = $this->db->query($sql);
        return $result;
    }

    function InsertPago(Object $pago)
    {

        $sql = "INSERT INTO sma_payments (id, date, sale_id, paid_by,amount, 
        created_by,type) 
        VALUES (NULL, current_timestamp(), $pago->sale_id,'$pago->paid_by',$pago->amount,$pago->created_by,
        '$pago->type')";

        $result = $this->db->query($sql);
        if ($result) {
            return $this->db->insert_id;
        }

        return $result;
    }
 	
	function UpdateVentaPago($status,$idsales,$paid)
    {

        $sql = "UPDATE sma_sales SET payment_status = '$status' , paid= $paid WHERE id = $idsales ";

        $result = $this->db->query($sql);
        return $result;
    }

	function restarVariantsProduct(Object $resta)
    {

        $sql = "UPDATE sma_product_variants SET quantity = (SELECT (sma_pro_var.quantity - $resta->quantity) 
        FROM (SELECT * FROM sma_product_variants) AS sma_pro_var WHERE sma_pro_var.product_id = $resta->product_id AND name = '$resta->variants' ) WHERE product_id = $resta->product_id AND name = '$resta->variants'";

            
        $result = $this->db->query($sql);
        return $result;
    }

    function getWareHouseProductVariants($option)
    {

        $sql = "SELECT product_id FROM sma_warehouses_products_variants WHERE option_id  = $option";

        

        $result = $this->db->query($sql);
        return $result;
    }

    function restarWareHouseProductVariants(Object $resta)
    {

        $sql = "UPDATE sma_warehouses_products_variants SET quantity = (SELECT (sma_war_pro_var.quantity - $resta->quantity) 
        FROM (SELECT * FROM sma_warehouses_products_variants) AS sma_war_pro_var WHERE sma_war_pro_var.product_id = $resta->product_id AND sma_war_pro_var.option_id =  $resta->option_id ) WHERE product_id = $resta->product_id AND option_id = $resta->option_id";


        $result = $this->db->query($sql);
        return $result;
    }

	function obtenerReferencia($referencia)
    {
        $sql = "SELECT reference_no FROM sma_sales               
                WHERE reference_no = '$referencia' ";

        $ref = $this->db->query($sql);
        return $ref;
    }

	function getCaracteresCliente()
    {

        $sql =  "SELECT id,customer FROM sma_sales ";
        $result = $this->db->query($sql);
        return $result;
    }

    function UpdateCorrecion($id,$name)
    {

        $sql =  "UPDATE sma_sales SET customer =  '$name' WHERE id = $id";

        
        $result = $this->db->query($sql);

        return $result;
    }

	function getSalesItems()
    {

        $sql =  "SELECT * FROM sma_sale_items ";

        
        $result = $this->db->query($sql);

        return $result;
    }

    function UpdatePrecio(Object $items)
    {

        $sql =  "UPDATE sma_sale_items SET real_unit_price = $items->real_unit_price WHERE id = $items->id";

        
        $result = $this->db->query($sql);

        return $result;
    }
	
	 function UpdatePrecioUnit(Object $items)
    {

        $sql =  "UPDATE sma_sale_items SET subtotal = $items->sub_total, real_unit_price = $items->real_unit_price, 
        net_unit_price = $items->net_unit_price, option_id = NULL, item_tax = $items->item_tax, unit_price = $items->unit_price WHERE id = $items->id";
        
        $result = $this->db->query($sql);

        return $result;
    }	
	
}
