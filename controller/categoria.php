<?php 
/* los controladores son las clases que conectan el modelo con cada archivo de
sincronizacion, enviandole la respuesta que se requiere*/
require_once 'models/categoriaQBN.php';

class CategoriaController{ 

    public function getCategoria(){ 
       
        $cat = new CategoriaQBN();
        $cate = $cat->getCategoria();

        return $cate;
        
    }
    public function getCategoriaID(){ 
       
        $cat = new CategoriaQBN();
        $cate = $cat->getCategoriaID();

        return $cate;
        
    }
    public function getSubCategoria($idp){ 
       
        $cats = new CategoriaQBN();
        $cates = $cats->getSubCategoria($idp);

        return $cates;
        
    }

    public function getSubSubCategoria($idps){ 
       
        $catss = new CategoriaQBN();
        $catess = $catss->getSubSubCategoria($idps);

        return $catess;
        
    }



}