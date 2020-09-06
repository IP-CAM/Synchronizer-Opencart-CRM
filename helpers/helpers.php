<?php
//Helpers es una clase de funciones para hacer determinada funcion

class Helpers
{
	

    //slug: crea un alias de un nombre,limpia tildes y espacios.
    function slug(String $titles)
    {

       
        $search = ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N','.' => ''];
        
        $title = strtr($titles, $search);
        
        $title = trim(strtolower($title));
        
        $title = str_replace(' ', '-', $title);

        return $title;
       

    }

    //MoverImagen : cojera una imagen dependiendo de la ruta donde esta 
    //la renombrar y la enviara a la ruta de destino. todo con la ayude de 
    //las librerias de imagen Upload.php,Image_lib.php


    function MoverImagen($dir)
    {

        $upload = new system_settings();
        $info = new SplFileInfo($dir);  
        $photo = '';
       
        if(is_file($dir)){
           
            $rdir = dirname($dir);
            $open = opendir($rdir);
            $dire = 'C:\xampp\tmp\/';
            $info = new SplFileInfo($dir);
            
            //Abro el directorio que voy a leer
            
            //Recorro el directorio para leer los archivos que tiene
             while (($file = readdir($open)) !== false) {
                //Leo todos los archivos    excepto . y ..
                if ($file == $info->getFilename()) {
                   
                   if (strlen($file) > 0) {

                        $info = new SplFileInfo($file);
                        
                        //remplazo \ por /
                        $img = getimagesize($rdir.'/' . $file);
                        $size_img = filesize($rdir.'/'. $file);
                        $dire = str_replace('/', '', $dire);

                        //creo un FILE con los datos que obtengo de la imgen
                        //de esa forma podre hacer uso de la libreria
                        $_FILES['userfile']['name'] = $file;
                        $files = explode('.' . $info->getExtension(), $file);
                        $_FILES['userfile']['type'] = $img['mime'];
                        $_FILES['userfile']['tmp_name'] = $dire . 'php' . $files[0] . '.tmp';

                        $_FILES['userfile']['error'] = 0;
                        $_FILES['userfile']['size'] = $size_img;
                        $_FILES['userfile']['width'] = @getimagesize($rdir.'/' . $file)[0];
                        $_FILES['userfile']['height'] = @getimagesize($rdir.'/' . $file)[1];
                        
                       $photo =  $upload->add_img($_FILES,$rdir);

                       return $photo;
                    }
                }
            }
        }else{
            return $photo;
        }
    }

}
