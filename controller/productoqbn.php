    <?php
    /* los controladores son las clases que conectan el modelo con cada archivo de
    sincronizacion, enviandole la respuesta que se requiere*/
    require_once 'models/productoQBN.php';

    class ProductControllerQBN{


        function getProductoCat($category_id,$parent_id,$product_id){

            $pro = new ProductsQBN();
            $proc = $pro->getProductoCat($category_id,$parent_id,$product_id);
            return $proc;

        }
        function obtenerParent($category_id){

            $pro = new ProductsQBN();
        
            $parent_id = $pro->obtenerParent($category_id);

            return $parent_id;

        }

        function getProducToCate($idpro){

            $pro = new ProductsQBN();
        
            $parent_id = $pro->getProducToCate($idpro);

            return $parent_id;

        }

        function getIdProduct(){

            $pro = new ProductsQBN();
        
            $proc = $pro->getIdProduct();

            return $proc;

        }

        function realProducCat($product_id){
            $pro = new ProductsQBN();
            
            $proc = $pro->realProducCat($product_id);

            return $proc;
        }
    	
    	function realProducDesc($fecha){
            $pro = new ProductsQBN();
            
            $proc = $pro->realProducDesc($fecha);

            return $proc;
        }
    

    }