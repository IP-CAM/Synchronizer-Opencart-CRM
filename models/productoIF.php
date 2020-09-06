<?php
require_once 'config/conexion.php';
class ProductsIF
{

    function __construct()
    {
		//instancio la conxion del crm para insertar datos
        $this->db = DataBaseIF::connect();
    }

    function insertProduct(Object $prod)
    {

        $sql =  "INSERT INTO sma_products (id,code,name,unit,cost,price,image,category_id,subcategory_id,quantity,tax_rate,details,barcode_symbology,product_details,brand,slug,weight) 
                VALUES($prod->product_id,'$prod->model','$prod->name',$prod->unit,$prod->cost,$prod->price,'$prod->image',$prod->category_id,$prod->subcategory_id,
                $prod->quantity,$prod->tax_class_id,'$prod->details','$prod->barcode_symbology','$prod->description',
                $prod->brand,'$prod->slug',$prod->weight_class_id)";
		
    

        $save = $this->db->query($sql);
    	
    
        $result = false;
        if ($save) {
            $result = $prod->product_id;
            return $result;
        }
        return $result;
    }

    function getExistProductsAlmacen()
    {
        $sql = "SELECT id,code,quantity,cost,name,tax_rate FROM sma_products";
        
        $proc = $this->db->query($sql);
        return $proc;
    }


    function deleteProducts(){
        $sql = "DELETE FROM sma_products";
        
        $proc = $this->db->query($sql);
        return $proc;
    }

    function getExistProduct($idp)
    {
        $sql = "SELECT id FROM sma_products                
                WHERE id = $idp";
        
        $proc = $this->db->query($sql);
        return $proc;
    }

    function getUltimProduc()
    {
        $sql = "SELECT count(id) as limite  FROM sma_products";
        $proc = $this->db->query($sql);
        return $proc;
    }
	
	function insertProductVariants(Object $prodvariants)
    {

        $sql =  "INSERT INTO sma_product_variants (id,product_id,name,cost,price,quantity) 
                VALUES($prodvariants->id,$prodvariants->product_id,
                '$prodvariants->name',$prodvariants->cost,$prodvariants->price,$prodvariants->quantity)";


        
        $save = $this->db->query($sql);
        $result = false;
        if ($save) {
            return $this->db->insert_id;
        }
        return $result;
    }

    function getProVar($id){
        $sql = "SELECT *  FROM sma_product_variants WHERE id = $id ";
        
        $proc = $this->db->query($sql);
        return $proc;
    }

    function getProWareVar($idvar){
        $sql = "SELECT *  FROM sma_warehouses_products_variants WHERE option_id = $idvar ";
        
        $proc = $this->db->query($sql);
        return $proc;
    }

    function insertProductWarehousesVariants(Object $prodvariants)
    {

        $sql =  "INSERT INTO sma_warehouses_products_variants
                (option_id,product_id,warehouse_id,quantity,rack) 
                VALUES($prodvariants->id,$prodvariants->product_id,
                '$prodvariants->warehouse_id',$prodvariants->quantity,$prodvariants->rack)";


        
        $save = $this->db->query($sql);
        $result = false;
        if ($save) {
            return $this->db->insert_id;
        }
        return $result;
    }
	
	function getDescripProduct()
    {

        $sql =  "SELECT id,product_details FROM sma_products";
        $result = $this->db->query($sql);
        return $result;
    }

    function UpdateDesc($details,$id)
    {

        $sql =  "UPDATE sma_products SET product_details =  '$details' WHERE id = $id ";

        
        $result = $this->db->query($sql);

        return $result;
    }

	function getExistProductsCode($code)
    {
        $sql = "SELECT tr.name as iva ,p.* FROM sma_products p
        INNER JOIN sma_tax_rates tr on tr.id = p.tax_rate
        WHERE p.code = '$code' ";

        
        
        $proc = $this->db->query($sql);
        return $proc;
    }

	function getProVarPrice($name, $product_id){

        $sql = "SELECT *  FROM sma_product_variants WHERE product_id  = $product_id AND name = '$name'";
            
        $proc = $this->db->query($sql);
   		
    	
    
        return $proc;
    }

  	function getCombo($code)
    {
        $sql = "SELECT * FROM sma_products 
        WHERE code = '$code' AND type = 'combo' ";

        $proc = $this->db->query($sql);
        return $proc;
    }
	
	function getComboSplit($code)
    {
        $sql = "SELECT * FROM sma_products 
        WHERE code LiKE '$code%' AND type = 'combo' ";

        
        $proc = $this->db->query($sql);
        return $proc;
    }


    function getItemsCombo($product_id)
    {
        $sql = "SELECT * FROM sma_combo_items 
        WHERE product_id = '$product_id' ";
        
        $proc = $this->db->query($sql);
        return $proc;
    }
	
	function getExistProductsCodes($code)
    {
        $sql = "SELECT p.* FROM sma_products p
        WHERE p.code = '$code'";
                
        $proc = $this->db->query($sql);
        return $proc;
    }

	function updateProduct(Object $prod)
    {
        
        $sql =  "UPDATE sma_products SET name = '$prod->name' , price = '$prod->price' WHERE code = '$prod->model' ";
        $save = $this->db->query($sql);
    
        $result = false;
        if ($save) {
            $result = true;
            return $result;
        }
        return $result;
    }

	function updateProductVariants(Object $prodvariants)
    	{

        $sql =  "UPDATE sma_product_variants SET name = '$prodvariants->name' WHERE id = $prodvariants->option_value_id ";


        $save = $this->db->query($sql);
        $result = false;
        if ($save) {
            return $prodvariants->option_value_id;
        }
        return $result;
    	}

		function getProVarPriceID($product_id){

        $sql = "SELECT *  FROM sma_product_variants WHERE product_id  = $product_id";
        
        $proc = $this->db->query($sql);
        return $proc;
    }
	
	function getOpcionName($name){

        $sql = "SELECT *  FROM sma_product_variants WHERE name  = '$name' ";
    
        
        $proc = $this->db->query($sql);
        return $proc;
    }

	
}
