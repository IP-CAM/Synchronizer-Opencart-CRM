<?php 
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/categoriaIF.php';

class CategoriaControllerIF{ 

    public function getId($idps){ 
       
        $cat = new CategoriaIF();
        $id = $cat->getId($idps);
        return $id;
        
    }

    public function delete(){ 
       
        $cat = new CategoriaIF();
        $id = $cat->delete();
        return $id;
        
    }
    

    public function insertCategoria($data){ 

      
        $cat = new CategoriaIF();
        $cate = '';
        if(is_object($data)){          
            $cate = $cat->insertCategoria($data);
            return $cate;
        }   
        return $cate;
    }

}