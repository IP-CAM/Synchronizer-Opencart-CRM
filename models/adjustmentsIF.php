<?php
/*Los modelos son clases donde se ejecutan las consultas
estos envian una respuesta al controlador */
class AdjustmentsIF
{

    function __construct()
    {
        //instancio la conxion del crm para insertar datos
        $this->db = DataBaseIF::connect();
    }


    function Insert(Object $datos)
    {

        $sql = "INSERT INTO sma_adjustments (date,
        reference_no,warehouse_id,note,attachment,created_by,updated_by,updated_at,count_id)
         VALUES(NOW(),'$datos->reference_no',$datos->warehouse_id,'',$datos->attachment,$datos->created_by,
         $datos->updated_by,$datos->updated_at,$datos->count_id)";
         
        $result = $this->db->query($sql);
        if($result){
            return $this->db->insert_id;
        }
        
       return $result;
        
    }

    function deleteAdjustmentsIF(){
        $sql = "DELETE FROM sma_adjustments";
        
        $adj = $this->db->query($sql);
        return $adj;
    }
}
