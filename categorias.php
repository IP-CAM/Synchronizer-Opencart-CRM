<?php
//require de librerias y controlasdores
require_once 'libraries/Upload.php';
require_once 'libraries/Image_lib.php';
require_once 'system_settings.php';
require_once 'controller/categoria.php';
require_once 'controller/categoriaif.php';
require_once 'helpers/helpers.php';
require_once 'config/config.php';

//instncia de clases
$cate = new CategoriaController();
$catif = new CategoriaControllerIF();
//obtengo las catgorias 
$catp = $cate->getCategoria();


while ($catpa = $catp->fetch_object()) {

    //Helpers es una clase de funciones para hacer determinada funcion
    $help = new Helpers();
    //obtengo la ruta donde guardo las imagenes concatendo con el nombre de la imagen             
    $img = (str_replace('//', '/', from . '/' . $catpa->image));

    //obtengo los id de cada categoria del crm pasando el id de
    //la categoria de la tienda para ver si existen
    $id = $catif->getId($catpa->category_id);


    if ($id->num_rows > 0) {
       // echo "ya existe ".$id->fetch_object()->id."<br>";
    } else {

        //si no existe
        //creo un objeto para pasar todos los parametros de categorias
        $categoria = new stdClass();
        $categoria->category_id = $catpa->category_id;
        //slug es una funcion para limpiar espacios, tildes, etc
        // y determina un alias esa categoria ejm : "ropa-bebe"
       
        $categoria->code = $catpa->category_id . '-' . $help->slug($catpa->name);
        $categoria->name = $catpa->name;
        //la funcion MoverImagn funciona con las librerias de imagenes Upload.php,Image_lib.php
        //le paso la ruta completa del directorio donde se guardan 
        $categoria->image =  $help->MoverImagen($img);
        $categoria->parent_id = $catpa->parent_id;
        $categoria->slug = $help->slug($catpa->name);
        $categoria->description = $catpa->description;
     	
    
        $cat = $catif->insertCategoria($categoria);
    
    
        ////var_dump($cat);
    }
}
