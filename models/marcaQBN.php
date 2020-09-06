<?php
/*Los modelos son clases donde se ejecutan las consultas
estos envian una respuesta al controlador */
require_once 'config/conexion.php';
class MarcaQBN
{

    function __construct()
    {
        //instancio la conxion de la tienda para obtener datos
        $this->db = DataBaseQBN::connect();
        $this->alias = aliasdb;
    }

    function getMarcaQBN()
    {

        $sql = "SELECT * FROM {$this->alias}manufacturer";
        $result = $this->db->query($sql);
        return $result;
    }
}
