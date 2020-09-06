<?php

require_once 'config/conexion.php';

class MarcaIF{

        public function __construct()
        {		 
				//instancio la conxion del crm para insertar datos
                $this->db = DataBaseIF::connect();
        }

        function deleteMarcaIF(){
            $sql = "DELETE FROM sma_brands";
            
            $brand = $this->db->query($sql);
            return $brand;
        }

        function insertMarc(Object $marcas){
                
                $sql = "INSERT INTO sma_brands(id,code,name,image,slug,description)
                VALUES($marcas->id,'$marcas->code','$marcas->name','$marcas->image','$marcas->slug','$marcas->description')";

                
                $brand = $this->db->query($sql);
                return $brand;
            }
        

}
