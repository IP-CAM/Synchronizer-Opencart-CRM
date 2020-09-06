<?php

require_once 'config/conexion.php';
class ClienteIF
{

    function __construct()
    {
        $this->db = DataBaseIF::connect();
    }

    function getCliente($email)
    {

        $sql = "SELECT id,email FROM sma_companies WHERE email = '$email' ";
        $result = $this->db->query($sql);

        return $result;
    }
    
    function Insert(Object $data)
    {

       $sql = "INSERT INTO sma_companies (group_id,group_name,
        customer_group_id,customer_group_name,name,company,vat_no,address,city,state,
        postal_code,country,phone,email,cf1,cf2,cf3,cf4,cf5,
        cf6,invoice_footer,payment_term,logo,award_points,
        deposit_amount,price_group_id,price_group_name,
        gst_no) VALUES ($data->group_id,'$data->group_name',
        $data->customer_group_id,'$data->customer_group_name','$data->name','$data->company','$data->vat_no','$data->address','$data->city','$data->state',
        '$data->postal_code','$data->country','$data->phone','$data->email','$data->cf1','$data->cf2','$data->cf3','$data->cf4','$data->cf5',
        '$data->cf6',";

        $sql .= $data->invoice_footer == 'NULL' ? 'NULL' : "'$data->invoice_footer'";

        $sql.=",$data->payment_term,'$data->logo',$data->award_points,
             $data->deposit_amount,$data->price_group_id,'$data->price_group_name','$data->gst_no')";

        $save = $this->db->query($sql);
        if ($save) {
            return $this->db->insert_id;
        }
        return $save;
    }

	function getCaracteresCliente()
    {

        $sql =  "SELECT id,address FROM sma_companies WHERE id > 6";
        $result = $this->db->query($sql);
        return $result;
    }


    function UpdateCorrecion($id,$address)
    {

        $sql =  "UPDATE sma_companies SET address =  '$address' WHERE id = $id";

        
        $result = $this->db->query($sql);

        return $result;
    }
}
