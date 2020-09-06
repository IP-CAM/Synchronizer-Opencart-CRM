<?php
/*Los modelos son clases donde se ejecutan las consultas
estos envian una respuesta al controlador */
require_once 'config/conexion.php';
class OptionQbn {


    function __construct()
    {
        $this->db = DataBaseQBN::connect();
        $this->alias = aliasdb;
    }

    

    function getOptions($idop){

        $sql = "SELECT * FROM  {$this->alias}option_description WHERE language_id = 2 AND option_id = $idop";
        $rows = $this->db->query($sql);

        return $rows;
    }

    

    function getOptionValues($idop){

        $sql = "SELECT * FROM  {$this->alias}option_value_description WHERE option_value_id  = $idop 
        AND language_id = 2 ORDER BY option_id ASC";

        $rows = $this->db->query($sql);

        return $rows;
    }


    function getProductOption($idpr){

        $sql = "SELECT * FROM  {$this->alias}product_option_value WHERE product_id = $idpr";
        
        $rows = $this->db->query($sql);

        return $rows;
    }

}