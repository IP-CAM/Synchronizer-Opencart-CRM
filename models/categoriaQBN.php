<?php
/*Los modelos son clases donde se ejecutan las consultas
estos envian una respuesta al controlador */
require_once 'config/conexion.php';

class CategoriaQBN{

        public function __construct()
        {
                //instancio la conxion de la tienda para obtener datos
                $this->db = DataBaseQBN::connect();
                $this->alias = aliasdb;
        }
     

        public function getCategoria(){
                
                $sql = "SELECT {$this->alias}category.category_id, {$this->alias}category_description.name,
                {$this->alias}category_description.description ,{$this->alias}category.image, {$this->alias}category.parent_id 
                FROM {$this->alias}category
                INNER JOIN {$this->alias}category_description on {$this->alias}category_description.category_id = {$this->alias}category.category_id
                WHERE {$this->alias}category_description.language_id = 2 ORDER BY {$this->alias}category.category_id ASC";

                
                $categoria = $this->db->query($sql);
                return $categoria;
        }

        public function getCategoriaID(){
                $sql = "SELECT {$this->alias}category.category_id, {$this->alias}category.parent_id
                FROM {$this->alias}category";

                
                $categoria = $this->db->query($sql);
                return $categoria;
        }

        public function getSubCategoria($idp){

                $sql = "SELECT {$this->alias}category.category_id, {$this->alias}category_description.name,
                {$this->alias}category_description.description ,{$this->alias}category.image, {$this->alias}category.parent_id 
                FROM {$this->alias}category
                INNER JOIN {$this->alias}category_description on {$this->alias}category_description.category_id = {$this->alias}category.category_id
                WHERE {$this->alias}category.parent_id = $idp AND {$this->alias}category_description.language_id = 2";

                $categoriasub = $this->db->query($sql);
                return $categoriasub;
        }

        public function getSubSubCategoria($idps){

                $sql = "SELECT {$this->alias}category.category_id, {$this->alias}category_description.name,
                {$this->alias}category_description.description ,{$this->alias}category.image, {$this->alias}category.parent_id 
                FROM {$this->alias}category
                INNER JOIN {$this->alias}category_description on {$this->alias}category_description.category_id = {$this->alias}category.category_id
                WHERE {$this->alias}category.parent_id = $idps AND {$this->alias}category_description.language_id = 2";

                $categoriasubs = $this->db->query($sql);
                return $categoriasubs;
        }

        

}