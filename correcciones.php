<?php

require_once 'controller/productoif.php';
require_once 'controller/clienteif.php';
require_once 'controller/sales.php';

$proif = new ProductControllerIF();
$clieif = new ClienteControllerIF();
$sale = new SalesControllerIF();
$desc = $proif->getDescripProduct();


/*while($descrip = $desc->fetch_object()){
    
    if(!empty($descrip->product_details)){
        
     $resul = $proif->UpdateDesc(html_entity_decode($descrip->product_details),$descrip->id);

      var_dump($resul);
    }
}*/


$clie = $clieif->getCaracteresCliente();

while($cliente = $clie->fetch_object()){
      
   $resul = $clieif->UpdateCorrecion($cliente->id,utf8_decode($cliente->address));

    //var_dump($resul);
  
}

/*
$clie = $sale->getCaracteresCliente();

while($cliente = $clie->fetch_object()){
      
   $resul = $sale->UpdateCorrecion($cliente->id,utf8_decode($cliente->customer));

    var_dump($resul);
  
}*/