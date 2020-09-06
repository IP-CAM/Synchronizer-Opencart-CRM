<?php
/*Los modelos son clases donde se ejecutan las consultas
estos envian una respuesta al controlador */
require_once 'config/conexion.php';
class ProductsQBN
{

    function __construct()
    {
        //instancio la conxion de la tienda para obtener datos
        $this->db = DataBaseQBN::connect();
        $this->alias = aliasdb;
    }

    function getProductoCat($category_id, $parent_id, $product_id)
    {

        $sql = "SELECT {$this->alias}product_to_category.product_id, {$this->alias}category.category_id,
           {$this->alias}category.parent_id, {$this->alias}product_description.*,{$this->alias}product.*
            FROM {$this->alias}product_to_category 
            INNER JOIN {$this->alias}category on {$this->alias}category.category_id = {$this->alias}product_to_category.category_id 
            INNER JOIN {$this->alias}product on {$this->alias}product.product_id = {$this->alias}product_to_category.product_id
            INNER JOIN {$this->alias}product_description on {$this->alias}product_description.product_id = {$this->alias}product.product_id 
            WHERE {$this->alias}product_description.language_id = 2 
            AND {$this->alias}category.category_id = $category_id 
            AND {$this->alias}category.parent_id = $parent_id 
            AND {$this->alias}product.product_id = $product_id 
            ORDER BY {$this->alias}product_to_category.product_id ASC ";

		

        $proc = $this->db->query($sql);
        return $proc;
    }

    function getProducToCate($idpro)
    {
        $sql = "SELECT ptc.product_id, ptc.category_id, c.parent_id  FROM {$this->alias}product_to_category ptc
        INNER JOIN {$this->alias}category c on c.category_id = ptc.category_id WHERE ptc.product_id = $idpro";
		
    	
        $idp = $this->db->query($sql);
        return $idp;
    }

    function getIdProduct()
    {

        $sql = "SELECT * FROM {$this->alias}product WHERE product_id != -1 ORDER BY product_id ASC";

        $idp = $this->db->query($sql);
        return $idp;
    }

    function obtenerParent($category_id)
    {

        $sql = "SELECT parent_id FROM {$this->alias}category WHERE category_id = $category_id";

        $parentid = $this->db->query($sql);
        return $parentid;
    }

    function realProducCat($product_id)
    {

        $sql = "SELECT {$this->alias}product_description.*,{$this->alias}product.* FROM {$this->alias}product 
                INNER JOIN {$this->alias}product_description on {$this->alias}product_description.product_id = {$this->alias}product.product_id 
                WHERE {$this->alias}product_description.language_id = 2 
                AND {$this->alias}product.product_id = $product_id ";
    
   
   
    

        $id_pro = $this->db->query($sql);

        return $id_pro;
    }
	
	function realProducDesc($fecha)
    {

        $sql = "SELECT {$this->alias}product_description.*,{$this->alias}product.* FROM {$this->alias}product 
                INNER JOIN {$this->alias}product_description on {$this->alias}product_description.product_id = {$this->alias}product.product_id 
                WHERE {$this->alias}product_description.language_id = 2 AND oc_product.date_modified >= '$fecha' ";
    
        $id_pro = $this->db->query($sql);

        return $id_pro;
    }


}
