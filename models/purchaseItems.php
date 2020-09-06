<?php
/*Los modelos son clases donde se ejecutan las consultas
estos envian una respuesta al controlador */
require_once 'config/conexion.php';
class purchaseItems{

    function __construct()
    {   
        $this->db = DataBaseIF::connect();
        
    }

    function Insert(object $data){

        $sql = "INSERT INTO sma_purchase_items (purchase_id,transfer_id,product_id, 
                            product_code,product_name,option_id,
                            net_unit_cost,quantity,warehouse_id,
                            item_tax,tax_rate_id,tax,
                            discount,item_discount,expiry,
                            subtotal,quantity_balance,date,
                            status,unit_cost,real_unit_cost,
                            quantity_received,supplier_part_no,purchase_item_id,
                            product_unit_id,product_unit_code,unit_quantity,
                            gst,cgst,sgst,igst) VALUES (NULL,NULL,$data->product_id,'$data->product_code','$data->name',
                            NULL,$data->net_unit_cost,$data->quantity,$data->warehouse_id,$data->item_tax,$data->tax_rate,'$data->tax',NULL,
                            NULL,NULL,0,$data->quantity,NOW(),'received',0,0,$data->quantity,NULL,NULL,
                            $data->product_unit_id,'Ud',$data->quantity,NULL,NULL,NULL,NULL)";
 

        $result = $this->db->query($sql);   
        
        return $result;
    }
}