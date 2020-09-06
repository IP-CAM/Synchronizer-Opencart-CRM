<?php
/*Los modelos son clases donde se ejecutan las consultas
estos envian una respuesta al controlador */
require_once 'config/conexion.php';
class VariantsIF {


    function __construct()
    {
        $this->db = DataBaseIF::connect();
    }

    function issetOption($idoption){
        $sql = "SELECT id,name FROM sma_variants WHERE id = $idoption";
        
        $result = $this->db->query($sql);

        return $result;
    }

	function issetOptionName($name){
        $sql = "SELECT id,name FROM sma_variants WHERE name = '$name'";
        
        $result = $this->db->query($sql);

        return $result;
    }

    function Insert(Object $variantval){

        
    $sql = "INSERT INTO sma_variants (id,name) VALUES($variantval->option_value_id,'$variantval->name')";
    
    
    $result = $this->db->query($sql);

   if ($result) {
        return $this->db->insert_id;
    }
    return $result;
    }
	
	function update(Object $variantval){

        
        $sql = "UPDATE sma_variants SET name = '$variantval->name'  WHERE id = $variantval->option_value_id";
        
        echo $sql."<br>";
        
        $result = $this->db->query($sql);
    
       if ($result) {
            return $variantval->option_value_id;
        }
        return false;
        }



}