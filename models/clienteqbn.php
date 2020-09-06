<?php
/*Los modelos son clases donde se ejecutan las consultas
estos envian una respuesta al controlador */
require_once 'config/conexion.php';

class ClienteQBN
{

    public function __construct()
    {
        //instancio la conxion de la tienda para obtener datos
        $this->db = DataBaseQBN::connect();
    }


   
}
