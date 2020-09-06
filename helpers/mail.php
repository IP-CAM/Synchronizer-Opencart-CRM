<?php

class Mail{


    function enviarEmail($json){

        $para      = 'info@interiberica.com';
        $titulo    = 'Error de opciones';
        $mensaje   =  $json;
        $cabeceras = 'From: info@interiberica.com';
        
        mail($para, $titulo, $mensaje, $cabeceras);
    }
   
}