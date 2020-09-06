<?php
/*Los modelos son clases donde se ejecutan las consultas
estos envian una respuesta al controlador */
class AdjustmentItemsIF
{

    function __construct()
    {
    //instancio la conxion del crm para insertar datos
        $this->db = DataBaseIF::connect();
    }


    function Insert(Object $datos)
    {

        $sql = "INSERT INTO sma_adjustment_items (adjustment_id,
        product_id,option_id,quantity,warehouse_id,serial_no,type)
         VALUES($datos->adjustment_id,$datos->product_id,$datos->option_id,
         $datos->quantity,$datos->warehouse_id,
         '','$datos->type')";
        
        $result = $this->db->query($sql);
        if ($result) {
            return $this->db->insert_id;
        }

        return $result;
    }

    function deleteAdjustmentsItems()
    {
        $sql = "DELETE FROM sma_adjustment_items";

        $adji = $this->db->query($sql);
        return $adji;
    }
}
