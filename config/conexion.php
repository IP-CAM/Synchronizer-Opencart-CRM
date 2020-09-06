<?php
//conexiones a bd
class DataBaseQBN{

    public static function connect(){
        $db = new mysqli('localhost','root','','bd_opencart');
        $db->query("SET NAMES 'utf-8'");
        return $db;
    }
}

class DataBaseIF{
    public static function connect(){
        $db = new mysqli('localhost','root','','bd_crm');
        $db->query("SET NAMES 'utf-8'");
        return $db;
    }
}