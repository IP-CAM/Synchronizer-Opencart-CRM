<?php
//conexiones a bd
class DataBaseQBN{

    public static function connect(){
        $db = new mysqli('localhost','mascotasalfalfa','BG+j]rcUu4<t','ww2');
        $db->query("SET NAMES 'utf-8'");
        return $db;
    }
}

class DataBaseIF{
    public static function connect(){
        $db = new mysqli('localhost','mascotasalfalfa','BG+j]rcUu4<t','crm');
        $db->query("SET NAMES 'utf-8'");
        return $db;
    }
}