<?php
require_once 'config/conexion.php';

class CategoriaIF{

        public function __construct()
        {
			//instancio la conxion del crm para insertar datos
                $this->db = DataBaseIF::connect();
        }
        

        public function getId($idps){

                $sql = "SELECT id FROM sma_categories WHERE id = $idps";

                $id = $this->db->query($sql);
                return $id;
        }

        public function insertCategoria(Object $data){ 

               
                $sql = "INSERT INTO sma_categories (id,code,name,image,parent_id,slug,description) 
                VALUES($data->category_id,'$data->code','$data->name','$data->image',$data->parent_id,'$data->slug','$data->description') ";
       
        		
       
                $save = $this->db->query($sql);
       
                $result = false;
                if($save){
                        $result = $data->category_id;
                }
                return $result;
        }

        public function delete(){

                $sql = "DELETE FROM sma_categories";

                $id = $this->db->query($sql);
                return $id;
        }

}