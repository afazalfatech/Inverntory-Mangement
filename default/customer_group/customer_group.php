<?php

include_once "../../files/config.php";

class customergroup{
 

public $customergroup_id;
public $customergroup_code;
public $customergroup_name;
public $customergroup_status;
public $customergroup_date;
private $db;

// --------------------------------------------------------------------------------------------------------------------'

function __construct(){
          
    $this->db=new mysqli(host,un,pw,db1);
}

// ----INSERT NEW CUSTOMER GROUP------------------------------------------------------------------------------------------------------------------


function insert_customer_group(){
    $cus_groupcode=$_POST["cust_grcode"];
    $cus_groupname=$_POST["cust_grname"];

    $sql_check="SELECT * FROM customer_group WHERE customergroup_code = '$cus_groupcode' OR customergroup_name=' $cus_groupname' ";
    $res_code=$this->db->query($sql_check);
    //echo $sql_check;
        if($res_code->num_rows==0){
                $sql="INSERT INTO customer_group (customergroup_code,customergroup_name) VALUES('$this->customergroup_code','$this->customergroup_name')";
                echo $sql;
                 $this->db->query($sql);
                return true;}
        else {
            return false;
        }

}

// ----EDIT CUSTOMER GROUP------------------------------------------------------------------------------------------------------------------


function get_all_customer_group(){

    $sql="SELECT * FROM customer_group WHERE customergroup_status='ACTIVE' ";
   // echo $sql;
    $result=$this->db->query($sql);

    $cus_grp_array=array();

    while($row=$result->fetch_array()){

        $cus_grp_item = new customergroup();

        $cus_grp_item->customergroup_id=$row["customergroup_id"];
        $cus_grp_item->customergroup_code=$row["customergroup_code"];
        $cus_grp_item->customergroup_name=$row["customergroup_name"];
        $cus_grp_item->customergroup_status=$row["customergroup_status"];
      

        $cus_grp_array[]=$cus_grp_item;
    }

    return $cus_grp_array;
}

// ----GET ALL CUSTOMER GROUP------------------------------------------------------------------------------------------------------------------



function get_customer_group_by_id($cus_grp_id){

    $sql="SELECT * FROM customer_group WHERE customergroup_id = $cus_grp_id";
    echo $sql;
    $result=$this->db->query($sql);
    $row=$result->fetch_array();

    $cus_grp_item = new customergroup();

    $cus_grp_item->customergroup_id=$row["customergroup_id"];
    $cus_grp_item->customergroup_code=$row["customergroup_code"];
    $cus_grp_item->customergroup_name=$row["customergroup_name"];
    $cus_grp_item->customergroup_status=$row["customergroup_status"];
    return $cus_grp_item;
}


// ---------------------------------------------------------------------------------------------------------------------



function edit_customer_group($cus_grp_id){

    $sql="UPDATE customer_group  SET 
    customergroup_code='$this->customergroup_code', customergroup_name='$this->customergroup_name'
    WHERE customergroup_id=$cus_grp_id ";

    echo $sql;
    $this->db->query($sql);
    return true;


}

//-------------------------------------------------------------------------------------------------------------------

function delete_customer_group($customergroup_id){
    $sql_check="SELECT * FROM customer WHERE customer_group =$customergroup_id";
    $result=$this->db->query ($sql_check);

    if($result->num_rows==0){
        $sql="UPDATE customer_group SET customergroup_status='INACTIVE' WHERE customergroup_id=$customergroup_id ";
        echo $sql;
        $this->db->query($sql);
        return true;
    }else{
        return false;
    }

    


}

//----------------------------------------------------------------------------------------------------------------------------

function get_customer_group_by_code($cus_grp_id){

    $sql="SELECT * FROM customer_group WHERE customergroup_code = '$cus_grp_id'";
    //echo $sql;
    $result=$this->db->query($sql);
    $row=$result->fetch_array();

    $cus_grp_item = new customergroup();

    $cus_grp_item->customergroup_id=$row["customergroup_id"];
    $cus_grp_item->customergroup_code=$row["customergroup_code"];
    $cus_grp_item->customergroup_name=$row["customergroup_name"];
    $cus_grp_item->customergroup_status=$row["customergroup_status"];

       
    return $cus_grp_item;
}

//.......................................................................................
function get_customer_group_by_name($cus_grp_name){

    $sql="SELECT * FROM customer_group WHERE customergroup_name = '$cus_grp_name'";
    //echo $sql;
    $result=$this->db->query($sql);
    $row=$result->fetch_array();

    $cus_grp_item = new customergroup();

    $cus_grp_item->customergroup_id=$row["customergroup_id"];
    $cus_grp_item->customergroup_code=$row["customergroup_code"];
    $cus_grp_item->customergroup_name=$row["customergroup_name"];
    $cus_grp_item->customergroup_status=$row["customergroup_status"];
    return $cus_grp_item;
}



}