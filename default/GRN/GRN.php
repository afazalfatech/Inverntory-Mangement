<?php
    include_once "../../files/config.php";
    include_once "../purchase_order/purchaseorder.php";
    include_once "../purchase_requisition/purchase_requisition.php";
    include_once "../GRN/grn_item.php";

    class GRN
    {
        public $grn_id;
        public $grn_puch_orderid;	
        public $grn_ref_no;
        public $grn_received_loc;	
        public $grn_status;
        // public $grn_current_status;
        public $grn_date;
        public $db;

        function __construct()
        {
            $this->db=new mysqli(host,un,pw,db1);
            return true;
        }

        // Add a new grn.....................................................................................................
        function insert_grn()
        {

            $SQL="INSERT INTO grn (grn_puch_orderid,grn_received_loc,grn_date,grn_ref_no) VALUES
            ('$this->grn_puch_orderid','$this->grn_received_loc','$this->grn_date','$this->grn_ref_no')";

            $this->db->query($SQL);
            // echo $SQL;
            $grnid=$this->db->insert_id;
            return $grnid;
        }
        // Genearte the grn code.............................................................................................. 
        function grn_code($grn_date)
        {
            $sql="SELECT COUNT(*) AS count FROM `grn` WHERE MONTH(grn_date)= EXTRACT(MONTH FROM '$grn_date') ";
        
                $sql1="SELECT  EXTRACT(MONTH FROM '$grn_date') AS grn_month ";
                $sql2="SELECT  EXTRACT(YEAR FROM '$grn_date') AS grn_year ";
        
                $result = $this->db->query($sql);
                $result1 = $this->db->query($sql1);
                $result2 = $this->db->query($sql2);
        
                $count= 0;
        
        
                while($row=$result->fetch_array()){
        
                    $count = $row["count"];
                }
        
                
                $month = "";
    
                while($row=$result1->fetch_array()){
    
                    $month = $row["grn_month"];
                }
    
                $month =sprintf("%02d", $month);
    
                $year = "";
    
                while($row=$result2->fetch_array()){
    
                    $year = $row["grn_year"];
                }
    
          
                
    
                $count = $count + 1 ;
                $count = sprintf("%04d", $count);
        
        
                //  $year = date("Y");
        
        
                $code = "GRN".  substr($year, 2, 2 ) . $month . $count;  
        
        
                return $code;
        
        
            }

            // Product batch
            function product_batch($prod_id)
            {
                $sql="SELECT COUNT(*) AS count FROM `grn` WHERE grn_item_prodid=$prod_id";
                $result = $this->db->query($sql);
                $count= 0;
        
        
                while($row=$result->fetch_array()){
        
                    $count = $row["count"];
                }
                $count = $count + 1 ;
                $count = sprintf("%04d", $count);
                $code = "PB".$prod_id.$count;  
                return $code;


            }
    

        // Edit a grn...............................................................................................................
        function edit_grn($id)
        {
            $SQL="UPDATE grn SET grn_received_loc='$this->grn_received_loc' WHERE grn_id='$id'";
            echo $SQL;
            $this->db->query($SQL);
            return true;
        }

        // Delete a grn...................................................................................................
        function delete_grn()
        {

        }

        // view all the grn.................................................................................................
        function get_all_grn()
        {
             //$SQL="SELECT * FROM grn WHERE grn_status='ACTIVE'";
            $SQL="SELECT grn.grn_id,grn.grn_puch_orderid,grn.grn_ref_no,grn.grn_received_loc,grn.grn_status,grn.grn_date,supplier.supplier_name
            FROM grn INNER JOIN purchase_order on grn.grn_puch_orderid=purchase_order.purchaseorder_id INNER JOIN purchase_request on purchase_order.purchaserorder_requestid=purchase_request.purchaserequest_id
            INNER JOIN supplier on purchase_request.purchaserequest_supplier=supplier.supplier_id WHERE grn_status='ACTIVE'";
            // echo $SQL;
            $result=$this->db->query($SQL);
            $grn_array=array();

            while($row=$result->fetch_array())
            {
                $grn=new GRN();
                $grn->grn_id=$row["grn_id"];
                $grn->grn_puch_orderid=$row["grn_puch_orderid"];
                $grn->grn_ref_no=$row["grn_ref_no"];
                $grn->grn_received_loc=$row["grn_received_loc"];
                $grn->grn_status=$row["grn_status"];
                $grn->grn_date=$row["grn_date"];
                $grn->grn_supplier=$row["supplier_name"];

                $grn_array[]=$grn;
            }
            return $grn_array;
        }

        // View the grn by its id...........................................................................................
        function get_grn_byid($grnid)
        {
            $SQL="SELECT * FROM grn WHERE grn_id=$grnid AND grn_status='ACTIVE'";
            $result=$this->db->query($SQL);

            $row=$result->fetch_array();
            $grn=new GRN();
            $grn->grn_id=$row["grn_id"];
            $grn->grn_puch_orderid=$row["grn_puch_orderid"];
            $grn->grn_ref_no=$row["grn_ref_no"];
            $grn->grn_received_loc=$row["grn_received_loc"];
            $grn->grn_status=$row["grn_status"];
            $grn->grn_date=$row["grn_date"];
            return $grn;
        }

        // Join po,pr to get supplier name
        function get_all_grn_by_poid1($id)
        {
            $SQL="SELECT purchase_order.purchaseorder_id,purchase_order.purchaserorder_requestid,purchase_order_item.po_item_orderid,
            purchase_order_item.po_item_productid,purchase_order_item.po_item_qty,purchase_order_item.po_item_price,purchase_order_item.po_item_discount,purchase_request.purchaserequest_supplier,supplier.supplier_name
            FROM grn INNER JOIN purchase_order on grn.grn_puch_orderid=purchase_order.purchaseorder_id INNER JOIN purchase_order_item on purchase_order.purchaseorder_id=purchase_order_item.po_item_orderid inner join purchase_request on purchase_order.purchaserorder_requestid=purchase_request.purchaserequest_id
            INNER JOIN supplier on purchase_request.purchaserequest_supplier=supplier.supplier_id WHERE  	purchaseorder_status='ACTIVE' AND  	purchaseorder_id='$id'";
            $result=$this->db->query($SQL);
            // echo ($SQL);
            $grn_po_array=array();

            while($row=$result->fetch_array())
            {
                $grn_po=new purchaseorder();
                $grn_po->purchaseorder_id=$row["purchaseorder_id"];
                // $grn_po->purchaseorder_requestid=$row["purchaseorder_requestid"];
                $grn_po->purchaseorder_item_orderid=$row["po_item_orderid"];
                $grn_po->purchaseorder_itemid=$row["po_item_productid"];
                $grn_po->purchaseorder_qty=$row["po_item_qty"];
                $grn_po->purchaseorder_itemprice=$row["po_item_price"];
                $grn_po->purchaseorder_itemdiscount=$row["po_item_discount"];
                // $grn_po->purchaseorder_itemfinalprice=$row["po_item_finalprice"];
                // $grn_po->purchaseorder_itemfinalprice=round(($row["po_item_price"] * $row["po_item_qty"]) - ($row["po_item_price"] *$row["po_item_qty"]* $row["po_item_discount"]/100),2);
                $grn_po->purchaseorder_itemfinalprice=round(($row['po_item_qty']*$row['po_item_price'])-($row['po_item_qty']*$row['po_item_price']*$row['po_item_discount']/100),2);

                $grn_po->purchaseorder_supid=$row["purchaserequest_supplier"];
                $grn_po->purchaseorder_supplier=$row["supplier_name"];

                $grn_po_array[]=$grn_po;

            }
            return $grn_po_array;

        }

    

        // Joining grn,po,pr and supplier to get supplier name
        function get_all_grn_by_grnid($id)
        {
            $SQL="SELECT grn.grn_id,purchase_order.purchaseorder_id,purchase_order.purchaserorder_requestid,purchase_order_item.po_item_orderid,
            purchase_order_item.po_item_productid,purchase_order_item.po_item_qty,purchase_order_item.po_item_price,purchase_order_item.po_item_discount,purchase_request.purchaserequest_supplier,supplier.supplier_name
            FROM grn INNER JOIN purchase_order on grn.grn_puch_orderid=purchase_order.purchaseorder_id INNER JOIN purchase_order_item on purchase_order.purchaseorder_id=purchase_order_item.po_item_orderid inner join purchase_request on purchase_order.purchaserorder_requestid=purchase_request.purchaserequest_id
            INNER JOIN supplier on purchase_request.purchaserequest_supplier=supplier.supplier_id WHERE grn_status='ACTIVE' AND grn_id='$id'";
            $result=$this->db->query($SQL);
            // echo ($SQL);
            $row=$result->fetch_array();
        
                $grn_po=new GRN();
                $grn_po->purchaseorder_id=$row["purchaseorder_id"];
                // $grn_po->purchaseorder_requestid=$row["purchaseorder_requestid"];
                $grn_po->purchaseorder_item_orderid=$row["po_item_orderid"];
                $grn_po->purchaseorder_itemid=$row["po_item_productid"];
                $grn_po->purchaseorder_qty=$row["po_item_qty"];
                $grn_po->purchaseorder_itemprice=$row["po_item_price"];
                $grn_po->purchaseorder_itemdiscount=$row["po_item_discount"];
                // $grn_po->purchaseorder_itemfinalprice=$row["po_item_finalprice"];
                $grn_po->purchaseorder_itemfinalprice=round(($row['po_item_qty']*$row['po_item_price'])-($row['po_item_qty']*$row['po_item_price']*$row['po_item_discount']/100),2);
                $grn_po->purchaseorder_supid=$row["purchaserequest_supplier"];
                $grn_po->purchaseorder_supplier=$row["supplier_name"];

                return  $grn_po;

        }

        // joining po item table and product table......................................................................
        function  get_all_grn_by_poid($id)
        {
            $SQL="SELECT *, product.product_name,product.product_inventory_val FROM purchase_order_item INNER JOIN product on purchase_order_item.po_item_productid=product.product_id
             WHERE po_item_status='ACTIVE' AND 	po_item_currentstatus='PENDING' AND po_item_orderid='$id'";
            $result=$this->db->query($SQL);
            $grn_po_array=array();

            while($row=$result->fetch_array())
            {
                $grn_po=new purchaseorder();
                $grn_po->purchaseorder_item_id=$row["po_item_id"];
                $grn_po->purchaseorder_item_orderid=$row["po_item_orderid"];
                $grn_po->purchaseorder_item_prodid=$row["po_item_productid"];
                $grn_po->purchaseorder_productinventory=$row['product_inventory_val'];
                $grn_po->purchaseorder_qty=$row["po_item_qty"];
                $grn_po->purchaseorder_itemprice=$row["po_item_price"];
                $grn_po->purchaseorder_itemdiscount=$row["po_item_discount"];
                $grn_po->purchaseorder_itemsubtotal=round(($row['po_item_qty']*$row['po_item_price']),2);
                // $grn_po->purchaseorder_itemfinalprice=$row["po_item_finalprice"];
                $grn_po->purchaseorder_itemfinalprice=round(($row['po_item_qty']*$row['po_item_price'])-($row['po_item_qty']*$row['po_item_price']*$row['po_item_discount']/100),2);
                $grn_po->purchaseorder_itemname=$row["product_name"];

                $grn_po_array[]=$grn_po;
            }
            return $grn_po_array;
        }

        // join the pr table and supplier............................................................................
        // function get_purchsup_by_prid($pr_id){

        //     $sql="SELECT * FROM purchase_request INNER JOIN supplier on purchase_request.purchaserequest_supplier=supplier.supplier_id WHERE purchaserequest_status='ACTIVE' AND purchaserequest_supplier='$pr_id' ";
        //     //echo $sql;
        //     $result=$this->db->query($sql);
        //     $row=$result->fetch_array();
        //     // $supp=new supplier ();
        //     $purchaserequest_item = new purchaserequest();
        
        //     $purchaserequest_item->purchaserequest_=$row["purchaserequest_date"];
        //     $purchaserequest_item->purchaserequest_suppid=$row["purchaserequest_supplier"];
        //     $purchaserequest_item->purchaserequest_suppname=$row["supplier_name"];
        //     $purchaserequest_item->purchaserequest_date=$row["purchaserequest_date"];
        

        //     return $purchaserequest_item;
        // }

        //     return $purchaserequest_item;
        // }
    

// ------------REPORT--------------------------------------------------------------------------------------------




function grn_report()
{
     //$SQL="SELECT * FROM grn WHERE grn_status='ACTIVE'";
    $SQL="SELECT grn.grn_id,grn.grn_puch_orderid,grn.grn_ref_no,grn.grn_received_loc,grn.grn_status,grn.grn_date,
    grn_item.grn_item_productid,product.product_name,product.product_code,product_group.group_name,grn_item.grn_item_qty,grn_item.grn_item_price,
    grn_item.grn_item_discount,product_type.ptype_name,supplier.supplier_name,location.location_name
    FROM grn 
    JOIN grn_item ON grn.grn_id = grn_item.grn_item_grnid
    JOIN product ON grn_item.grn_item_productid= product.product_id
    JOIN product_group ON product_group.group_id = product.product_id
    JOIN product_type ON  product.product_type = product_type.ptype_id
    JOIN purchase_order on grn.grn_puch_orderid = purchase_order.purchaseorder_id
    JOIN purchase_request on purchase_order.purchaserorder_requestid=purchase_request.purchaserequest_id
    JOIN supplier on purchase_request.purchaserequest_supplier=supplier.supplier_id
    JOIN location ON grn.grn_received_loc = location.location_id
  
   

    
     WHERE grn_status='ACTIVE'";
    // echo $SQL;
    $result=$this->db->query($SQL);
    $grn_array=array();

    while($row=$result->fetch_array())
    {
        $grn=new GRN();
        $grn->grn_id=$row["grn_id"];
        $grn->grn_puch_orderid=$row["grn_puch_orderid"];
        $grn->grn_ref_no=$row["grn_ref_no"];
        $grn->grn_received_loc=$row["grn_received_loc"];
        $grn->grn_status=$row["grn_status"];
        $grn->grn_date=$row["grn_date"];
        $grn->grn_supplier=$row["supplier_name"];
        $grn->grn_location=$row["location_name"];
      
      
        $grn->grn_item_qty =$row["grn_item_qty"];
        $grn->grn_item_price =$row["grn_item_price"];
        $grn->grn_item_discount =$row["grn_item_discount"];
        $grn->product_code =$row["product_code"];
        $grn->product_name =$row["product_name"];
        $grn->group_name =$row["group_name"];
        $grn->ptype_name =$row["ptype_name"];

        $grn_array[]=$grn;
    }
    return $grn_array;
}



// --------------------------------------------


function filter_grn(){

    $filter_sup=$_POST['filter_sup'];
    $filter_product=$_POST['filter_product'];
    $filter_startdt=$_POST['filter_startdt'];
    $filter_enddt=$_POST['filter_enddt'];
    $filter_grp=$_POST['filter_grp'];
    $filter_type=$_POST['filter_type'];


    $sql="SELECT grn.grn_id,grn.grn_puch_orderid,grn.grn_ref_no,grn.grn_received_loc,grn.grn_status,grn.grn_date,
    grn_item.grn_item_productid,product.product_name,product.product_code,product_group.group_name,grn_item.grn_item_qty,grn_item.grn_item_price,
    grn_item.grn_item_discount,product_type.ptype_name,supplier.supplier_name,location.location_name
    FROM grn 
    JOIN grn_item ON grn.grn_id = grn_item.grn_item_grnid
    JOIN product ON grn_item.grn_item_productid= product.product_id
    JOIN product_group ON product_group.group_id = product.product_id
    JOIN product_type ON  product.product_type = product_type.ptype_id
    JOIN purchase_order on grn.grn_puch_orderid = purchase_order.purchaseorder_id
    JOIN purchase_request on purchase_order.purchaserorder_requestid=purchase_request.purchaserequest_id
    JOIN supplier on purchase_request.purchaserequest_supplier=supplier.supplier_id
    JOIN location ON grn.grn_received_loc = location.location_id
  
   

    
     WHERE grn_status='ACTIVE'";


    if($filter_sup!=-1){
        $sql.=" and supplier_name='$filter_sup'";
    }
    if($filter_product!=-1){
        $sql.=" and product_name='$filter_product'";
    }

    if($filter_grp!=-1){
        $sql.=" and group_name='$filter_grp'";
    }
    if($filter_type!=-1){
        $sql.=" and ptype_name='$filter_type'";
    }

    if($filter_startdt!='' && $filter_enddt!=''){
        $sql.="and grn_date BETWEEN  '".$_POST['filter_startdt']."' AND '".$_POST['filter_enddt']."' "; 
      
    }

    $result=$this->db->query($sql);
    $grn_array=array();

    echo $sql;

    while($row=$result->fetch_array())
    {
        $grn=new GRN();
        $grn->grn_id=$row["grn_id"];
        $grn->grn_puch_orderid=$row["grn_puch_orderid"];
        $grn->grn_ref_no=$row["grn_ref_no"];
        $grn->grn_received_loc=$row["grn_received_loc"];
        $grn->grn_status=$row["grn_status"];
        $grn->grn_date=$row["grn_date"];
        $grn->grn_supplier=$row["supplier_name"];
        $grn->grn_location=$row["location_name"];
      
      
        $grn->grn_item_qty =$row["grn_item_qty"];
        $grn->grn_item_price =$row["grn_item_price"];
        $grn->grn_item_discount =$row["grn_item_discount"];
        $grn->product_code =$row["product_code"];
        $grn->product_name =$row["product_name"];
        $grn->group_name =$row["group_name"];
        $grn->ptype_name =$row["ptype_name"];

        $grn_array[]=$grn;
    }
    return $grn_array;
}


    }
?>