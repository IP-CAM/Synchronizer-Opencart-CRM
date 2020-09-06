<?php
/*Los modelos son clases donde se ejecutan las consultas
estos envian una respuesta al controlador */
class AlmacenIF
{


    function __construct()
    {
        //instancio la conxion del crm para insertar datos
        $this->db = DataBaseIF::connect();
    }

    function insertAlmacen_uno(Object $almacens)
    {


        $sql = "INSERT INTO sma_warehouses_products (product_id,warehouse_id,
                quantity,rack,avg_cost) VALUES($almacens->product_id,
                $almacens->warehouse_id,$almacens->quantity,$almacens->rack,$almacens->avg_cost)";
       
        $result = $this->db->query($sql);
        if ($result) {
            return $this->db->insert_id;
        }
        return $result;
    }
    
    function insertAlmacen_dos(Object $almacens)
    {


        $sql = "INSERT INTO sma_warehouses_products (product_id,warehouse_id,
                quantity,rack,avg_cost) VALUES($almacens->product_id,
                $almacens->warehouse_id,$almacens->quantity,$almacens->rack,$almacens->avg_cost)";

      
        $result = $this->db->query($sql);
        if ($result) {
            return $this->db->insert_id;
        }
        return $result;
    }


    function deleteAlmacenIF()
    {
        $sql = "DELETE FROM sma_warehouses_products";

        $almc = $this->db->query($sql);
        return $almc;
    }
	
	   function issetProduct($idp)
    {
        $sql = "SELECT product_id FROM sma_warehouses_products WHERE product_id = $idp";

        $almc = $this->db->query($sql);
        return $almc;
    }
}
